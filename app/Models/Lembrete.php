<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lembrete extends Model
{
    /** @use HasFactory<\Database\Factories\LembreteFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'finish',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
