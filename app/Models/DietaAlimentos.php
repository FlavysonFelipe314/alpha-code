<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietaAlimentos extends Model
{
    /** @use HasFactory<\Database\Factories\DietaAlimentosFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'quantidade',
        'dieta_id',
    ];

    public function dieta(){
        return $this->belongsTo(Dieta::class);
    }
}
