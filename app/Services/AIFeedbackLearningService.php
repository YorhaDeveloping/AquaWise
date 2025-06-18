<?php

namespace App\Services;

use App\Models\ExpertFeedback;
use App\Models\Catch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AIFeedbackLearningService
{
    private $similarityThreshold = 0.7;

    public function getSimilarCasesSuggestions(array $catchData): array
    {
        // Get similar historical cases with expert feedback
        $similarCases = $this->findSimilarCases($catchData);
        
        if ($similarCases->isEmpty()) {
            // Fallback to traditional suggestion service if no similar cases found
            $traditionalService = new CatchAnalysisSuggestionService();
            return $traditionalService->getSuggestions($catchData);
        }

        return $this->generateAISuggestions($similarCases, $catchData);
    }

    private function findSimilarCases(array $catchData): Collection
    {
        return ExpertFeedback::with('catch')
            ->whereHas('catch', function ($query) use ($catchData) {
                $query->where(function ($q) use ($catchData) {
                    // Match similar weather conditions
                    $q->where('weather_conditions', 'like', '%' . ($catchData['weather_conditions'] ?? '') . '%');
                    
                    // Match similar quantity range (±20%)
                    if (isset($catchData['quantity'])) {
                        $minQuantity = $catchData['quantity'] * 0.8;
                        $maxQuantity = $catchData['quantity'] * 1.2;
                        $q->whereBetween('quantity', [$minQuantity, $maxQuantity]);
                    }

                    // Match similar size range (±20%)
                    if (isset($catchData['average_size'])) {
                        $minSize = $catchData['average_size'] * 0.8;
                        $maxSize = $catchData['average_size'] * 1.2;
                        $q->whereBetween('average_size', [$minSize, $maxSize]);
                    }

                    // Match species if available
                    if (isset($catchData['fish_species'])) {
                        $q->where('fish_species', $catchData['fish_species']);
                    }
                });
            })
            ->where('effectiveness_score', '>=', $this->similarityThreshold)
            ->orderBy('effectiveness_score', 'desc')
            ->limit(5)
            ->get();
    }

    private function generateAISuggestions(Collection $similarCases, array $currentCatch): array
    {
        $feedback = $this->aggregateExpertFeedback($similarCases);
        
        // Enhance feedback with current conditions
        $enhancedFeedback = $this->enhanceFeedbackWithContext($feedback, $currentCatch);
        
        return [
            'feedback' => $enhancedFeedback['feedback'],
            'recommendations' => $enhancedFeedback['recommendations'],
            'sustainability_rating' => $this->calculateSustainabilityRating($similarCases),
            'confidence_score' => $this->calculateConfidenceScore($similarCases),
            'based_on_expert_cases' => $similarCases->count()
        ];
    }

    private function aggregateExpertFeedback(Collection $cases): array
    {
        $aggregatedFeedback = [
            'weather' => [],
            'quantity' => [],
            'size' => [],
            'species' => [],
            'recommendations' => []
        ];

        foreach ($cases as $case) {
            // Aggregate feedback by category
            if ($case->weather_feedback) {
                $aggregatedFeedback['weather'][] = $case->weather_feedback;
            }
            if ($case->quantity_feedback) {
                $aggregatedFeedback['quantity'][] = $case->quantity_feedback;
            }
            if ($case->size_feedback) {
                $aggregatedFeedback['size'][] = $case->size_feedback;
            }
            if ($case->species_feedback) {
                $aggregatedFeedback['species'][] = $case->species_feedback;
            }
            if ($case->overall_recommendations) {
                $aggregatedFeedback['recommendations'][] = $case->overall_recommendations;
            }
        }

        // Process and combine feedback
        return [
            'feedback' => $this->processAggregatedFeedback($aggregatedFeedback),
            'recommendations' => $this->processAggregatedRecommendations($aggregatedFeedback['recommendations'])
        ];
    }

    private function processAggregatedFeedback(array $feedback): string
    {
        $combinedFeedback = [];
        
        foreach ($feedback as $category => $items) {
            if ($category !== 'recommendations' && !empty($items)) {
                // Use most frequent feedback for each category
                $frequencies = array_count_values($items);
                arsort($frequencies);
                $combinedFeedback[] = reset($frequencies);
            }
        }

        return implode("\n\n", array_filter($combinedFeedback));
    }

    private function processAggregatedRecommendations(array $recommendations): string
    {
        // Flatten all recommendations and count frequencies
        $allRecommendations = [];
        foreach ($recommendations as $recommendationSet) {
            $items = explode("\n", $recommendationSet);
            foreach ($items as $item) {
                if (trim($item)) {
                    $allRecommendations[] = trim($item);
                }
            }
        }

        $frequencies = array_count_values($allRecommendations);
        arsort($frequencies);

        // Take top 5 most common recommendations
        $topRecommendations = array_slice(array_keys($frequencies), 0, 5);
        return implode("\n", $topRecommendations);
    }

    private function enhanceFeedbackWithContext(array $feedback, array $currentCatch): array
    {
        // Add context-specific insights based on current conditions
        $contextualInsights = $this->generateContextualInsights($currentCatch);
        
        return [
            'feedback' => $feedback['feedback'] . "\n\n" . $contextualInsights['feedback'],
            'recommendations' => $feedback['recommendations'] . "\n" . $contextualInsights['recommendations']
        ];
    }

    private function generateContextualInsights(array $catchData): array
    {
        $insights = [
            'feedback' => "Based on current conditions:",
            'recommendations' => ""
        ];

        // Add seasonal context
        $season = $this->determineCurrentSeason();
        $insights['feedback'] .= "\nSeasonal factors for $season may affect fish behavior and catch patterns.";

        // Add time-based recommendations
        $timeOfDay = date('H');
        if ($timeOfDay < 10) {
            $insights['recommendations'] .= "\n• Early morning conditions are optimal for [specific species] activity";
        } elseif ($timeOfDay > 16) {
            $insights['recommendations'] .= "\n• Consider evening feeding patterns in your approach";
        }

        return $insights;
    }

    private function determineCurrentSeason(): string
    {
        $month = date('n');
        $seasons = [
            'Winter' => [12, 1, 2],
            'Spring' => [3, 4, 5],
            'Summer' => [6, 7, 8],
            'Fall' => [9, 10, 11]
        ];

        foreach ($seasons as $season => $months) {
            if (in_array($month, $months)) {
                return $season;
            }
        }

        return 'Unknown';
    }

    private function calculateSustainabilityRating(Collection $cases): string
    {
        $ratings = $cases->pluck('sustainability_rating');
        $ratingWeights = [
            'Good' => 3,
            'Concerning' => 2,
            'Critical' => 1
        ];

        $totalWeight = 0;
        $count = 0;

        foreach ($ratings as $rating) {
            if (isset($ratingWeights[$rating])) {
                $totalWeight += $ratingWeights[$rating];
                $count++;
            }
        }

        $averageWeight = $count > 0 ? $totalWeight / $count : 2;

        if ($averageWeight >= 2.5) return 'Good';
        if ($averageWeight >= 1.5) return 'Concerning';
        return 'Critical';
    }

    private function calculateConfidenceScore(Collection $cases): float
    {
        $baseScore = min(1.0, $cases->count() / 5); // Base score on number of similar cases
        $avgEffectiveness = $cases->avg('effectiveness_score');
        
        return round(($baseScore + $avgEffectiveness) / 2, 2);
    }
} 