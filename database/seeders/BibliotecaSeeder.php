<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Biblioteca;
use App\Models\User;

class BibliotecaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $user = User::factory()->create();
        }
        
        Biblioteca::factory()->count(8)->create([
            'user_id' => $user->id,
        ]);
    }
}
