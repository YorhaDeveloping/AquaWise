<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'fish_species',
        'care_instructions',
        'feeding_guide',
        'water_parameters',
        'common_diseases',
        'prevention_tips',
        'views',
        'status'
    ];

    protected $casts = [
        'water_parameters' => 'array',
        'views' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
