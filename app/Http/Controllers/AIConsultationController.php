<?php

namespace App\Http\Controllers;

use App\Models\CatchAnalysis;
use App\Services\AIConsultationService;
use Illuminate\Http\Request;

class AIConsultationController extends Controller
{
    protected $aiConsultationService;

    public function __construct(AIConsultationService $aiConsultationService)
    {
        $this->middleware('auth');
        $this->aiConsultationService = $aiConsultationService;
    }

    /**
     * Display the AI consultation form.
     */
    public function index()
    {
        return view('ai-consultation');
    }

    /**
     * Get AI consultation analysis based on fish species.
     */
    public function getConsultation(Request $request)
    {
        try {
            $request->validate([
                'fish_species' => 'required|string',
            ]);

            // Get expert-based recommendations for the fish species
            $similarCatches = CatchAnalysis::where('fish_species', $request->fish_species)
                ->with('expertReviews')
                ->get();

            $consultation = $this->aiConsultationService->generateConsultation([
                'similar_catches' => $similarCatches,
                'fish_species' => $request->fish_species
            ]);

            return response()->json($consultation);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error generating consultation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 