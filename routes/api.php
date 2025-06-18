<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpertFeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// User route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Expert Feedback Routes
    Route::prefix('expert-feedback')->group(function () {
        Route::get('/', [ExpertFeedbackController::class, 'index']);
        Route::post('/fish-catches/{fishCatch}', [ExpertFeedbackController::class, 'store']);
        Route::get('/fish-catches/{fishCatch}/suggestions', [ExpertFeedbackController::class, 'getSuggestions']);
        Route::get('/{feedback}', [ExpertFeedbackController::class, 'show']);
        Route::put('/{feedback}', [ExpertFeedbackController::class, 'update']);
        Route::delete('/{feedback}', [ExpertFeedbackController::class, 'destroy']);
    });
}); 