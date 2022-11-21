<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
    ];

    public function home() {
        return $this->hasOne(Team::class, 'id','home_team_id');
    }

    public function away() {
        return $this->hasOne(Team::class, 'id','away_team_id');
    }

    public function prediction() {
        return $this->hasOne(Prediction::class)->where('user_id',auth()->user()->id);
    }

    public function allPredictions() {
        return $this->hasMany(Prediction::class);
    }
}
