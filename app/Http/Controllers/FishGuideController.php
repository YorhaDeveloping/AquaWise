<?php

namespace App\Http\Controllers;

use App\Models\FishGuide;
use Illuminate\Http\Request;

class FishGuideController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:expert')->except(['index', 'show', 'storeComment', 'disable', 'disabled', 'enable']);
        $this->middleware('role:admin')->only(['disable', 'disabled', 'enable']);
    }

    /**
     * Disable the specified fish guide.
     */
    public function disable(FishGuide $fishGuide)
    {
        $this->authorize('disable', $fishGuide);
        
        $fishGuide->update(['status' => 'disabled']);

        return redirect()->back()
            ->with('success', 'Fish guide has been disabled.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guides = FishGuide::query()
            ->where(function ($query) {
                $query->where('status', 'published')
                      ->orWhere('user_id', auth()->id());
            })
            ->when(request('search'), function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . request('search') . '%')
                      ->orWhere('description', 'like', '%' . request('search') . '%')
                      ->orWhere('fish_species', 'like', '%' . request('search') . '%');
                });
            })
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('fish-guides.index', compact('guides'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fish-guides.create');
    }

    /**
     * Get AI-generated suggestions for the fish guide.
     */
    public function getSuggestions(Request $request)
    {
        $validated = $request->validate([
            'fish_species' => 'required|string|max:255'
        ]);

        $suggestions = $this->guideAnalysisService->generateGuideSuggestions($validated['fish_species']);
        
        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'fish_species' => 'required|string|max:255',
            'care_instructions' => 'required|string',
            'feeding_guide' => 'required|string',
            'water_parameters' => 'required|array',
            'water_parameters.temperature' => 'required|string',
            'water_parameters.ph' => 'required|string',
            'water_parameters.hardness' => 'required|string',
            'common_diseases' => 'nullable|string',
            'prevention_tips' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'confidence_score' => 'nullable|numeric'
        ]);

        // Add metadata about AI assistance if present
        if ($request->has('ai_assisted') && $request->boolean('ai_assisted')) {
            $validated['metadata'] = [
                'ai_assisted' => true,
                'confidence_score' => $validated['confidence_score'] ?? null,
                'expert_review_count' => $request->input('expert_review_count'),
                'catch_analysis_count' => $request->input('catch_analysis_count')
            ];
        }

        $guide = auth()->user()->fishGuides()->create($validated);

        return redirect()->route('fish-guides.show', $guide)
            ->with('success', 'Fish guide created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FishGuide $fishGuide)
    {
        if (!auth()->user()->hasRole('expert') && $fishGuide->status !== 'published') {
            abort(404);
        }

        $fishGuide->load(['user', 'comments.user', 'images']);
        $fishGuide->incrementViews();

        return view('fish-guides.show', ['guide' => $fishGuide]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FishGuide $fishGuide)
    {
        $this->authorize('update', $fishGuide);
        return view('fish-guides.edit', ['guide' => $fishGuide]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FishGuide $fishGuide)
    {
        $this->authorize('update', $fishGuide);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'fish_species' => 'required|string|max:255',
            'care_instructions' => 'required|string',
            'feeding_guide' => 'required|string',
            'water_parameters' => 'required|array',
            'water_parameters.temperature' => 'required|string',
            'water_parameters.ph' => 'required|string',
            'water_parameters.hardness' => 'required|string',
            'common_diseases' => 'nullable|string',
            'prevention_tips' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
        ]);

        $fishGuide->update($validated);

        return redirect()->route('fish-guides.show', $fishGuide)
            ->with('success', 'Fish guide updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FishGuide $fishGuide)
    {
        $this->authorize('delete', $fishGuide);
        
        $fishGuide->delete();

        return redirect()->route('fish-guides.index')
            ->with('success', 'Fish guide deleted successfully.');
    }

    /**
     * Archive the specified fish guide.
     */
    public function archive(FishGuide $fishGuide)
    {
        $this->authorize('update', $fishGuide);
        
        $fishGuide->update(['status' => 'archived']);

        return redirect()->route('fish-guides.show', $fishGuide)
            ->with('success', 'Fish guide archived successfully.');
    }

    /**
     * Publish the specified fish guide.
     */
    public function publish(FishGuide $fishGuide)
    {
        $this->authorize('update', $fishGuide);
        
        $fishGuide->update(['status' => 'published']);

        return redirect()->route('fish-guides.show', $fishGuide)
            ->with('success', 'Fish guide published successfully.');
    }

    /**
     * Store a new comment for the fish guide.
     */
    public function storeComment(Request $request, FishGuide $fishGuide)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $fishGuide->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return redirect()->route('fish-guides.show', $fishGuide)
            ->with('success', 'Comment added successfully.');
    }

    /**
     * Show all disabled fish guides for admin.
     */
    public function disabled()
    {
        $guides = \App\Models\FishGuide::where('status', 'disabled')->with('user')->latest()->paginate(10);
        return view('fish-guides.disabled', compact('guides'));
    }

    /**
     * Enable a disabled fish guide.
     */
    public function enable(FishGuide $fishGuide)
    {
        $this->authorize('enable', $fishGuide);
        $fishGuide->update(['status' => 'published']);
        return redirect()->back()->with('success', 'Fish guide enabled successfully.');
    }
}
