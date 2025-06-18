<?php

namespace App\Services;

use App\Models\CatchAnalysis;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AIConsultationService
{
    public function generateConsultation(array $data): array
    {
        try {
            $expertBasedAdvice = $this->generateExpertBasedAdvice($data['similar_catches'], $data['fish_species']);
            
            return [
                'expert_insights' => [
                    'recommendations' => $expertBasedAdvice['recommendations'] ?? [],
                    'success_patterns' => $expertBasedAdvice['patterns'] ?? ['Overall' => [
                        'success_rate' => 0,
                        'avg_quantity' => 0,
                        'count' => 0
                    ]],
                    'sustainability_tips' => $expertBasedAdvice['sustainability'] ?? [],
                ],
                'confidence_score' => $this->calculateConfidenceScore($data['similar_catches']),
                'fish_species' => $data['fish_species']
            ];
        } catch (\Exception $e) {
            Log::error('Error generating AI consultation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a properly structured response even in case of error
            return [
                'expert_insights' => [
                    'recommendations' => [],
                    'success_patterns' => ['Overall' => [
                        'success_rate' => 0,
                        'avg_quantity' => 0,
                        'count' => 0
                    ]],
                    'sustainability_tips' => [],
                ],
                'confidence_score' => 0,
                'fish_species' => $data['fish_species'] ?? ''
            ];
        }
    }

    private function generateExpertBasedAdvice(Collection $similarCatches, string $fishSpecies): array
    {
        $recommendations = [];
        $patterns = [];
        $sustainabilityTips = [];

        if ($similarCatches->isEmpty()) {
            return [
                'recommendations' => [
                    "No specific recommendations available for {$fishSpecies} yet.",
                    "Consider consulting local fishing guides for specific advice."
                ],
                'patterns' => ['Overall' => [
                    'success_rate' => 0,
                    'avg_quantity' => 0,
                    'count' => 0
                ]],
                'sustainability' => [
                    "No sustainability data available for {$fishSpecies} yet."
                ]
            ];
        }

        $processedRecommendations = [];
        $processedSustainabilityTips = [];

        foreach ($similarCatches as $catch) {
            foreach ($catch->expertReviews as $review) {
                if (!empty($review->recommendations)) {
                    // Split recommendations into individual sentences
                    $sentences = $this->splitIntoSentences($review->recommendations);
                    foreach ($sentences as $sentence) {
                        $sentence = trim($sentence);
                        if (empty($sentence)) continue;
                        
                        // Check if this recommendation is semantically similar to any existing ones
                        $isDuplicate = false;
                        foreach ($processedRecommendations as $existingRec) {
                            if ($this->isSimilarText($sentence, $existingRec)) {
                                $isDuplicate = true;
                                break;
                            }
                        }
                        if (!$isDuplicate) {
                            $processedRecommendations[] = $sentence;
                            $recommendations[] = $sentence;
                        }
                    }
                }
                if (!empty($review->sustainability_rating)) {
                    $tip = "Sustainability Rating: {$review->sustainability_rating} - {$review->expert_feedback}";
                    // Check if this sustainability tip is semantically similar to any existing ones
                    $isDuplicate = false;
                    foreach ($processedSustainabilityTips as $existingTip) {
                        if ($this->isSimilarText($tip, $existingTip)) {
                            $isDuplicate = true;
                            break;
                        }
                    }
                    if (!$isDuplicate) {
                        $processedSustainabilityTips[] = $tip;
                        $sustainabilityTips[] = $tip;
                    }
                }
            }

            // Analyze patterns
            $patterns[] = [
                'type' => 'catch_data',
                'quantity' => $catch->quantity,
                'success_rate' => $this->calculateSuccessRate($catch)
            ];
        }

        // Ensure we have at least some default recommendations if none were found
        if (empty($recommendations)) {
            $recommendations[] = "No specific recommendations available for {$fishSpecies} yet.";
            $recommendations[] = "Consider consulting local fishing guides for specific advice.";
        }

        // Ensure we have at least some default sustainability tips if none were found
        if (empty($sustainabilityTips)) {
            $sustainabilityTips[] = "No sustainability data available for {$fishSpecies} yet.";
        }

        return [
            'recommendations' => $recommendations,
            'patterns' => $this->aggregatePatterns($patterns),
            'sustainability' => $sustainabilityTips
        ];
    }

    /**
     * Split text into individual sentences
     */
    private function splitIntoSentences(string $text): array
    {
        // Remove any existing line breaks and extra spaces
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Split on sentence boundaries while preserving the delimiter
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        return array_map('trim', $sentences);
    }

    /**
     * Check if two text strings are semantically similar
     */
    private function isSimilarText(string $text1, string $text2): bool
    {
        // Convert to lowercase and remove punctuation for comparison
        $text1 = strtolower(preg_replace('/[^\w\s]/', '', $text1));
        $text2 = strtolower(preg_replace('/[^\w\s]/', '', $text2));

        // If texts are exactly the same after normalization
        if ($text1 === $text2) {
            return true;
        }

        // Calculate similarity using Levenshtein distance
        $maxLength = max(strlen($text1), strlen($text2));
        if ($maxLength === 0) {
            return true;
        }

        // For very short texts (less than 10 characters), require exact match
        if ($maxLength < 10) {
            return $text1 === $text2;
        }

        $levenshtein = levenshtein($text1, $text2);
        $similarity = 1 - ($levenshtein / $maxLength);

        // Consider texts similar if they have 85% or more similarity
        // Increased threshold for better accuracy
        return $similarity >= 0.85;
    }

    private function calculateSuccessRate(CatchAnalysis $catch): float
    {
        $totalReviews = $catch->expertReviews->count();
        if ($totalReviews === 0) {
            return 0.0;
        }

        $positiveReviews = $catch->expertReviews->filter(function ($review) {
            return $review->sustainability_rating === 'Good';
        })->count();

        return ($positiveReviews / $totalReviews) * 100;
    }

    private function aggregatePatterns(array $patterns): array
    {
        $aggregated = [
            'Overall' => [
                'success_rate' => 0,
                'count' => 0,
                'avg_quantity' => 0
            ]
        ];

        foreach ($patterns as $pattern) {
            $aggregated['Overall']['success_rate'] += $pattern['success_rate'];
            $aggregated['Overall']['avg_quantity'] += $pattern['quantity'];
            $aggregated['Overall']['count']++;
        }

        // Calculate averages
        if ($aggregated['Overall']['count'] > 0) {
            $aggregated['Overall']['success_rate'] /= $aggregated['Overall']['count'];
            $aggregated['Overall']['avg_quantity'] /= $aggregated['Overall']['count'];
        }

        return $aggregated;
    }

    private function calculateConfidenceScore(Collection $similarCatches): float
    {
        $totalCatches = $similarCatches->count();
        $totalReviews = $similarCatches->sum(function ($catch) {
            return $catch->expertReviews->count();
        });

        // Base confidence on amount of data available
        $baseConfidence = min(($totalCatches * 0.1) + ($totalReviews * 0.05), 0.9);
        
        // Add some randomness to avoid static scores
        $randomFactor = mt_rand(0, 100) / 1000; // Random factor between 0 and 0.1
        
        return round($baseConfidence + $randomFactor, 2);
    }
} 