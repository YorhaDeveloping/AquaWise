<?php

namespace App\Http\Controllers;

use App\Models\ExpertFeedback;
use App\Models\FishCatch;
use App\Services\ExpertFeedbackService;
use App\Services\AIFeedbackLearningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertFeedbackController extends Controller
{
    private $feedbackService;
    private $aiLearningService;

    public function __construct(ExpertFeedbackService $feedbackService, AIFeedbackLearningService $aiLearningService)
    {
        $this->feedbackService = $feedbackService;
        $this->aiLearningService = $aiLearningService;
        $this->middleware(['auth', 'verified']);
    }

    public function store(Request $request, FishCatch $fishCatch)
    {
        $request->validate([
            'weather_feedback' => 'nullable|string',
            'quantity_feedback' => 'nullable|string',
            'size_feedback' => 'nullable|string',
            'weight_feedback' => 'nullable|string',
            'species_feedback' => 'nullable|string',
            'overall_recommendations' => 'required|string',
            'sustainability_rating' => 'required|in:Good,Concerning,Critical'
        ]);

        try {
            $feedback = $this->feedbackService->submitFeedback(
                $request->all(),
                Auth::user(),
                $fishCatch
            );

            return response()->json([
                'message' => 'Expert feedback submitted successfully',
                'feedback' => $feedback
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error submitting expert feedback',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function getSuggestions(FishCatch $fishCatch)
    {
        try {
            $suggestions = $this->aiLearningService->getSimilarCasesSuggestions([
                'weather_conditions' => $fishCatch->weather_conditions,
                'quantity' => $fishCatch->quantity,
                'average_size' => $fishCatch->average_size,
                'fish_species' => $fishCatch->fish_species,
                'total_weight' => $fishCatch->total_weight
            ]);

            return response()->json([
                'suggestions' => $suggestions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error generating suggestions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $query = ExpertFeedback::with(['expert', 'fishCatch'])
            ->orderBy('created_at', 'desc');

        if ($request->has('expert_id')) {
            $query->where('expert_id', $request->expert_id);
        }

        if ($request->has('effectiveness_min')) {
            $query->where('effectiveness_score', '>=', $request->effectiveness_min);
        }

        if ($request->has('sustainability_rating')) {
            $query->where('sustainability_rating', $request->sustainability_rating);
        }

        $feedbacks = $query->paginate(15);

        return response()->json($feedbacks);
    }

    public function show(ExpertFeedback $feedback)
    {
        return response()->json([
            'feedback' => $feedback->load(['expert', 'fishCatch'])
        ]);
    }

    public function update(Request $request, ExpertFeedback $feedback)
    {
        $this->authorize('update', $feedback);

        $request->validate([
            'weather_feedback' => 'nullable|string',
            'quantity_feedback' => 'nullable|string',
            'size_feedback' => 'nullable|string',
            'weight_feedback' => 'nullable|string',
            'species_feedback' => 'nullable|string',
            'overall_recommendations' => 'required|string',
            'sustainability_rating' => 'required|in:Good,Concerning,Critical'
        ]);

        try {
            $feedback->update($request->all());

            return response()->json([
                'message' => 'Expert feedback updated successfully',
                'feedback' => $feedback->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating expert feedback',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(ExpertFeedback $feedback)
    {
        $this->authorize('delete', $feedback);

        try {
            $feedback->delete();

            return response()->json([
                'message' => 'Expert feedback deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting expert feedback',
                'error' => $e->getMessage()
            ], 422);
        }
    }
} 