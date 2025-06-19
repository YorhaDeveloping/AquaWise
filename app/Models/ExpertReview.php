<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'catch_analysis_id',
        'reviewer_id',
        'feedback',
        'sustainability_rating',
    ];

    /**
     * Get the catch analysis that owns this review.
     */
    public function catchAnalysis(): BelongsTo
    {
        return $this->belongsTo(CatchAnalysis::class);
    }

    /**
     * Get the expert who created the review.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the suggestions for this review.
     */
    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }
} 