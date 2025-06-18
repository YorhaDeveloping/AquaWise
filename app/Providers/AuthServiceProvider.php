<?php

namespace App\Providers;

use App\Models\FishGuide;
use App\Models\CatchAnalysis;
use App\Policies\FishGuidePolicy;
use App\Policies\CatchAnalysisPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        FishGuide::class => FishGuidePolicy::class,
        CatchAnalysis::class => CatchAnalysisPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
} 