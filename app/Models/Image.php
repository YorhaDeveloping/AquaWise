<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'fish_guide_id',
        'path',
        'caption'
    ];

    public function fishGuide()
    {
        return $this->belongsTo(FishGuide::class);
    }
}
