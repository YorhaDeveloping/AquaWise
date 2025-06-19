<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_review_id',
        'recommendations',
    ];

    public function expertReview(): BelongsTo
    {
        return $this->belongsTo(ExpertReview::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($suggestion) {
            // Prevent saving if 'recommendations' is a duplicate for the same review (case-insensitive)
            $exists = self::where('expert_review_id', $suggestion->expert_review_id)
                ->whereRaw('LOWER(recommendations) = ?', [strtolower($suggestion->recommendations)])
                ->exists();
            // Prevent saving if 'recommendations' contains 'similar case' or 'thought' (case-insensitive)
            $containsForbidden = preg_match('/similar case|thought/i', $suggestion->recommendations);
            if ($exists || $containsForbidden) {
                return false; // Cancel creation
            }
        });
    }
} 