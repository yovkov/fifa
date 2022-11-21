<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'home_score',
        'away_score',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
