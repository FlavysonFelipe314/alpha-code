<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietaSuplementos extends Model
{
    /** @use HasFactory<\Database\Factories\DietaSuplementosFactory> */
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
