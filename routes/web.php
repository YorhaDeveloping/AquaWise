<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatchAnalysisController;
use App\Http\Controllers\FishGuideController;
use App\Http\Controllers\WeatherForecastController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AIController;
use App\Http\Controllers\AIConsultationController;

Route::post('/ai-suggest', [AIController::class, 'getSuggestion']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Catch Analysis Routes
    Route::resource('catch-analyses', CatchAnalysisController::class);
    Route::patch('/catch-analyses/{catchAnalysis}/review', [CatchAnalysisController::class, 'review'])->name('catch-analyses.review');
    Route::patch('/catch-analyses/{catchAnalysis}/unreview', [CatchAnalysisController::class, 'unreview'])->name('catch-analyses.unreview');
    Route::get('/catch-analyses/{catchAnalysis}/suggestions', [CatchAnalysisController::class, 'getSuggestions'])
        ->name('catch-analyses.suggestions');
    
    // Weather Routes
    Route::get('/weather', [WeatherForecastController::class, 'index'])->name('weather.index');
    Route::get('/weather/data', [WeatherForecastController::class, 'getWeatherData'])->name('weather.data');
    Route::get('/weather/fetch', [WeatherForecastController::class, 'fetch'])->name('weather.fetch');

    // AI Consultation Routes
    Route::get('/ai-consultation', [AIConsultationController::class, 'index'])->name('ai.consultation.index');
    Route::post('/ai-consultation', [AIConsultationController::class, 'getConsultation'])->name('ai.consultation.analyze');

    // Fish Guide Routes
    Route::prefix('fish-guides')->name('fish-guides.')->group(function () {
        // Admin Routes
        Route::middleware('role:admin')->group(function () {
            Route::get('/disabled', [FishGuideController::class, 'disabled'])->name('disabled');
            Route::patch('/{fishGuide}/disable', [FishGuideController::class, 'disable'])->name('disable');
            Route::patch('/{fishGuide}/enable', [FishGuideController::class, 'enable'])->name('enable');
        });

        // Expert Routes
        Route::middleware('role:expert')->group(function () {
            Route::get('/create', [FishGuideController::class, 'create'])->name('create');
            Route::post('/', [FishGuideController::class, 'store'])->name('store');
            Route::get('/{fishGuide}/edit', [FishGuideController::class, 'edit'])->name('edit');
            Route::put('/{fishGuide}', [FishGuideController::class, 'update'])->name('update');
            Route::delete('/{fishGuide}', [FishGuideController::class, 'destroy'])->name('destroy');
            Route::patch('/{fishGuide}/archive', [FishGuideController::class, 'archive'])->name('archive');
            Route::patch('/{fishGuide}/publish', [FishGuideController::class, 'publish'])->name('publish');
        });

        // Public Routes (available to all authenticated users)
        Route::get('/', [FishGuideController::class, 'index'])->name('index');
        Route::get('/{fishGuide}', [FishGuideController::class, 'show'])->name('show');
        Route::post('/{fishGuide}/comments', [FishGuideController::class, 'storeComment'])->name('comments.store');
    });
});

Route::get('/team', function () {
    return view('team');
})->name('team');

require __DIR__.'/auth.php';
