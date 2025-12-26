<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'theme_colors',
        'plano_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'theme_colors' => 'array',
        ];
    }

    public function rituais()
    {
        return $this->hasMany(Ritual::class);
    }

    public function modoCaverna()
    {
        return $this->hasOne(ModoCaverna::class);
    }

    public function desafiosCaverna()
    {
        return $this->hasMany(DesafioCaverna::class);
    }

    public function pomodoros()
    {
        return $this->hasMany(Pomodoro::class);
    }

    public function tarefas()
    {
        return $this->hasMany(Tarefa::class);
    }

    public function tarefaColunas()
    {
        return $this->hasMany(TarefaColuna::class);
    }

    public function treinos()
    {
        return $this->hasMany(Treino::class);
    }

    public function rotinas()
    {
        return $this->hasMany(Rotina::class);
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class);
    }
}
