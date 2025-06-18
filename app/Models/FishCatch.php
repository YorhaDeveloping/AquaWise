<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FishCatch extends Model
{
    use SoftDeletes;

    protected $table = 'fish_catches';

    protected $fillable = [
        'user_id',
        'weather_conditions',
        'quantity',
        'average_size',
        'total_weight',
        'fish_species',
        'location',
        'environmental_conditions'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'average_size' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'location' => 'array',
        'environmental_conditions' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expertFeedbacks(): HasMany
    {
        return $this->hasMany(ExpertFeedback::class, 'catch_id');
    }
} 