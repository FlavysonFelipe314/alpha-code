<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objetivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'topic',
        'description',
        'deadline',
        'completed',
        'reminders',
        'user_id',
    ];

    protected $casts = [
        'deadline' => 'date',
        'completed' => 'boolean',
        'reminders' => 'array',
    ];
}
