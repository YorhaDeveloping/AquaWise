<?php

namespace App\Http\Controllers;

use App\Models\CatchAnalysis;
use App\Models\ExpertFeedback;
use App\Models\User;
use App\Models\ExpertReview;
use App\Services\CatchAnalysisSuggestionService;
use App\Services\AIFeedbackLearningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatchAnalysisController extends Controller
{
    protected $suggestionService;
    private $aiLearningService;

    public function __construct(
        CatchAnalysisSuggestionService $suggestionService,
        AIFeedbackLearningService $aiLearningService
    ) {
        $this->middleware('auth');
        $this->suggestionService = $suggestionService;
        $this->aiLearningService = $aiLearningService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $catchAnalyses = CatchAnalysis::with('user')
            ->when(!Auth::user()->hasRole('expert'), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('catch-analyses.index', compact('catchAnalyses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('catch-analyses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fish_species' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'total_weight' => 'required|numeric|min:0',
            'average_size' => 'nullable|numeric|min:0',
            'location' => 'required|string|max:255',
            'catch_date' => 'required|date',
            'weather_conditions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:10240', // Max 10MB
        ]);

        // Create catch analysis without image first
        $catchAnalysis = Auth::user()->catchAnalyses()->create($validated);

        // Handle image upload if present
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('catch-analyses', 'public');
            $catchAnalysis->update(['image_path' => $path]);
        }

        return redirect()->route('catch-analyses.show', $catchAnalysis)
            ->with('success', 'Catch analysis created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CatchAnalysis $catchAnalysis)
    {
        $this->authorize('view', $catchAnalysis);
        return view('catch-analyses.show', compact('catchAnalysis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CatchAnalysis $catchAnalysis)
    {
        $this->authorize('update', $catchAnalysis);
        return view('catch-analyses.edit', compact('catchAnalysis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CatchAnalysis $catchAnalysis)
    {
        $this->authorize('update', $catchAnalysis);

        $validated = $request->validate([
            'fish_species' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'total_weight' => 'required|numeric|min:0',
            'average_size' => 'nullable|numeric|min:0',
            'location' => 'required|string|max:255',
            'catch_date' => 'required|date',
            'weather_conditions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:10240', // Max 10MB
        ]);

        $catchAnalysis->update($validated);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('catch-analyses', 'public');
            $catchAnalysis->update(['image_path' => $path]);
        }

        return redirect()->route('catch-analyses.show', $catchAnalysis)
            ->with('success', 'Catch analysis updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CatchAnalysis $catchAnalysis)
    {
        $this->authorize('delete', $catchAnalysis);
        $catchAnalysis->delete();

        return redirect()->route('catch-analyses.index')
            ->with('success', 'Catch analysis deleted successfully.');
    }

    /**
     * Get suggestions for the catch analysis review.
     */
    public function getSuggestions(CatchAnalysis $catchAnalysis)
    {
        try {
            // Use all expert reviews for AI suggestions if there are at least 3
            $allReviews = ExpertReview::all();

            if ($allReviews->count() >= 3) {
                $feedback = '';
                $recommendations = '';
                $sustainabilityRatings = [];

                foreach ($allReviews as $review) {
                    $feedback .= ($review->feedback ?? '') . "\n";
                    $recommendations .= $review->suggestions->pluck('recommendations')->implode("\n") . "\n";
                    $sustainabilityRatings[] = $review->sustainability_rating;
                }

                // Calculate the most common sustainability rating
                $rating = array_count_values($sustainabilityRatings);
                arsort($rating);
                $sustainabilityRating = key($rating);

                // Calculate confidence between 98% and 100% based on number of cases
                $confidence = 0.98 + (min($allReviews->count(), 10) / 500);

                return response()->json([
                    'feedback' => $this->summarizeFeedback($feedback),
                    'recommendations' => $this->summarizeRecommendations($recommendations),
                    'sustainability_rating' => $sustainabilityRating,
                    'confidence_score' => $confidence,
                    'based_on_expert_cases' => $allReviews->count()
                ]);
            }

            // If not enough reviews, use predefined suggestions
            $suggestions = $this->suggestionService->getSuggestions([
                'weather_conditions' => $catchAnalysis->weather_conditions,
                'quantity' => $catchAnalysis->quantity,
                'average_size' => $catchAnalysis->average_size,
                'fish_species' => $catchAnalysis->fish_species,
                'total_weight' => $catchAnalysis->total_weight
            ]);

            if ($suggestions && isset($suggestions['feedback'], $suggestions['recommendations'], $suggestions['sustainability_rating'])) {
                return response()->json([
                    'feedback' => $suggestions['feedback'],
                    'recommendations' => $suggestions['recommendations'],
                    'sustainability_rating' => $suggestions['sustainability_rating'],
                    'confidence_score' => 0.99, // 99% confidence for predefined rules
                    'based_on_expert_cases' => 0
                ]);
            } else {
                return response()->json([
                    'feedback' => '',
                    'recommendations' => '',
                    'sustainability_rating' => '',
                    'confidence_score' => 0,
                    'based_on_expert_cases' => 0
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'feedback' => '',
                'recommendations' => '',
                'sustainability_rating' => '',
                'confidence_score' => 0,
                'based_on_expert_cases' => 0,
                'error' => 'Failed to generate suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add an expert review to the analysis.
     */
    public function review(Request $request, CatchAnalysis $catchAnalysis)
    {
        $this->authorize('review', $catchAnalysis);

        $validated = $request->validate([
            'feedback' => 'required|string',
            'recommendations' => 'required|string',
            'sustainability_rating' => 'required|in:Good,Concerning,Critical'
        ]);

        try {
            $review = ExpertReview::create([
                'catch_analysis_id' => $catchAnalysis->id,
                'reviewer_id' => auth()->id(),
                'feedback' => $validated['feedback'],
                'sustainability_rating' => $validated['sustainability_rating'],
            ]);

            $recommendations = preg_split('/\r?\n/', $validated['recommendations']);
            foreach ($recommendations as $rec) {
                $rec = trim($rec);
                if ($rec !== '') {
                    $review->suggestions()->create([
                        'recommendations' => $rec,
                    ]);
                }
            }

            return redirect()
                ->route('catch-analyses.show', $catchAnalysis)
                ->with('success', 'Expert review submitted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit review: ' . $e->getMessage());
        }
    }

    /**
     * Remove the expert's review from the analysis.
     */
    public function unreview(CatchAnalysis $catchAnalysis)
    {
        $this->authorize('unreview', $catchAnalysis);
        
        try {
            // Find and delete the expert's review
            ExpertReview::where('catch_analysis_id', $catchAnalysis->id)
                       ->where('reviewer_id', auth()->id())
                       ->delete();

            return redirect()->route('catch-analyses.show', $catchAnalysis)
                ->with('success', 'Review removed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove review: ' . $e->getMessage());
        }
    }

    private function summarizeFeedback(string $feedback): string
    {
        // Remove duplicate sentences and combine feedback
        $sentences = array_unique(array_filter(explode("\n", $feedback)));
        return implode("\n\n", array_slice($sentences, 0, 5)); // Limit to top 5 unique pieces of feedback
    }

    private function summarizeRecommendations(string $recommendations): string
    {
        // Remove duplicate recommendations and combine
        $lines = array_unique(array_filter(explode("\n", $recommendations)));
        return implode("\n", array_slice($lines, 0, 5)); // Limit to top 5 unique recommendations
    }
} 