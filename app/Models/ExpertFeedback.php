<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertFeedback extends Model
{
    protected $fillable = [
        'catch_id',
        'expert_id',
        'weather_feedback',
        'quantity_feedback',
        'size_feedback',
        'weight_feedback',
        'species_feedback',
        'overall_recommendations',
        'sustainability_rating',
        'effectiveness_score',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'effectiveness_score' => 'float'
    ];

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id');
    }

    public function catchAnalysis(): BelongsTo
    {
        return $this->belongsTo(CatchAnalysis::class, 'catch_id');
    }
} 