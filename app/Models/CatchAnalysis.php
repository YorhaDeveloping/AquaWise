<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatchAnalysis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'fish_species',
        'quantity',
        'total_weight',
        'average_size',
        'location',
        'catch_date',
        'weather_conditions',
        'notes',
        'image_path'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'catch_date' => 'date',
        'quantity' => 'integer',
        'total_weight' => 'float',
        'average_size' => 'float'
    ];

    /**
     * Get the user that owns the catch analysis.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all expert reviews for this catch analysis.
     */
    public function expertReviews(): HasMany
    {
        return $this->hasMany(ExpertReview::class);
    }

    /**
     * Calculate average weight per fish.
     */
    public function getAverageWeightAttribute(): float
    {
        return $this->quantity > 0 ? $this->total_weight / $this->quantity : 0;
    }

    /**
     * Check if this catch analysis is reviewed by a specific expert.
     */
    public function isReviewedBy(User $user): bool
    {
        return $this->expertReviews()->where('reviewer_id', $user->id)->exists();
    }

    /**
     * Get all unique reviewers.
     */
    public function reviewers()
    {
        return User::whereIn('id', $this->expertReviews->pluck('reviewer_id')->unique());
    }
}
