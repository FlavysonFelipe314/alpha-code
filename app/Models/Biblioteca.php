<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biblioteca extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'type',
        'status',
        'progress',
        'notes',
        'file_path',
        'file_type',
        'user_id',
    ];

    protected $casts = [
        'progress' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
