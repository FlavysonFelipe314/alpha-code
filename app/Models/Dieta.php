<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dieta extends Model
{
    /** @use HasFactory<\Database\Factories\DietaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'time',
        'day',
        'observation',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alimentos(){
        return $this->hasMany(DietaAlimentos::class);
    }
    
    public function suplementos(){
        return $this->hasMany(DietaSuplementos::class);
    }
}
