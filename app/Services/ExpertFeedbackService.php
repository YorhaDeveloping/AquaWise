<?php

namespace App\Services;

use App\Models\ExpertFeedback;
use App\Models\User;
use App\Models\FishCatch;
use Illuminate\Support\Facades\Log;

class ExpertFeedbackService
{
    public function submitFeedback(array $feedbackData, User $expert, FishCatch $fishCatch): ExpertFeedback
    {
        try {
            // Validate expert status
            if (!$this->isQualifiedExpert($expert)) {
                throw new \Exception('User is not qualified as an expert');
            }

            // Calculate effectiveness score based on expert's history
            $effectivenessScore = $this->calculateExpertEffectivenessScore($expert);

            // Create feedback record
            $feedback = ExpertFeedback::create([
                'catch_id' => $fishCatch->id,
                'expert_id' => $expert->id,
                'weather_feedback' => $feedbackData['weather_feedback'] ?? null,
                'quantity_feedback' => $feedbackData['quantity_feedback'] ?? null,
                'size_feedback' => $feedbackData['size_feedback'] ?? null,
                'weight_feedback' => $feedbackData['weight_feedback'] ?? null,
                'species_feedback' => $feedbackData['species_feedback'] ?? null,
                'overall_recommendations' => $feedbackData['overall_recommendations'],
                'sustainability_rating' => $feedbackData['sustainability_rating'],
                'effectiveness_score' => $effectivenessScore,
                'metadata' => $this->generateMetadata($feedbackData, $expert, $fishCatch)
            ]);

            // Log the feedback submission
            Log::info('Expert feedback submitted', [
                'expert_id' => $expert->id,
                'catch_id' => $fishCatch->id,
                'effectiveness_score' => $effectivenessScore
            ]);

            return $feedback;
        } catch (\Exception $e) {
            Log::error('Error submitting expert feedback', [
                'error' => $e->getMessage(),
                'expert_id' => $expert->id,
                'catch_id' => $fishCatch->id
            ]);
            throw $e;
        }
    }

    private function isQualifiedExpert(User $user): bool
    {
        // Check if user has expert role and necessary qualifications
        return $user->hasRole('expert') && 
               $user->expert_qualification_level >= 2 && 
               $user->expert_verification_status === 'verified';
    }

    private function calculateExpertEffectivenessScore(User $expert): float
    {
        $baseScore = 0.7; // Base score for verified experts

        // Factors that can increase the effectiveness score:
        $factors = [
            'experience_years' => min(($expert->years_of_experience ?? 0) * 0.05, 0.1),
            'feedback_rating' => min(($expert->average_feedback_rating ?? 0) * 0.05, 0.1),
            'contribution_score' => min(($expert->contribution_score ?? 0) * 0.02, 0.1)
        ];

        return min(1.0, $baseScore + array_sum($factors));
    }

    private function generateMetadata(array $feedbackData, User $expert, FishCatch $fishCatch): array
    {
        return [
            'expert_qualifications' => [
                'years_experience' => $expert->years_of_experience ?? 0,
                'specialization' => $expert->specialization ?? 'general',
                'certification_level' => $expert->expert_qualification_level ?? 1
            ],
            'feedback_context' => [
                'timestamp' => now()->toIso8601String(),
                'location' => $fishCatch->location ?? null,
                'season' => $this->determineSeason(),
                'environmental_conditions' => $fishCatch->environmental_conditions ?? []
            ],
            'analysis_factors' => [
                'considered_weather' => isset($feedbackData['weather_feedback']),
                'considered_quantity' => isset($feedbackData['quantity_feedback']),
                'considered_size' => isset($feedbackData['size_feedback']),
                'considered_species' => isset($feedbackData['species_feedback'])
            ]
        ];
    }

    private function determineSeason(): string
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
} 