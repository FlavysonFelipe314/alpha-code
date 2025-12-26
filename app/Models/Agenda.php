<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    /** @use HasFactory<\Database\Factories\AgendaFactory> */
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'date',
        'time',
        'duration',
        'category',
        'notes',
        'completed',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'completed' => 'boolean',
    ];
}
