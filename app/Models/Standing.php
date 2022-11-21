<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standing extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'team_id',
        'mp',
        'w',
        'l',
        'd',
        'gf',
        'ga',
        'gd',
        'pts',
    ];

    public function team() {
        return $this->hasOne(Team::class,'id','team_id');
    }
}
