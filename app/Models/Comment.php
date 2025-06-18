<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fish_guide_id',
        'content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fishGuide()
    {
        return $this->belongsTo(FishGuide::class);
    }
}
