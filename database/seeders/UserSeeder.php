<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria usuário padrão se não existir
        User::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Usuario Padrao',
                'email' => 'user@alphacode.com',
                'password' => Hash::make('password'),
            ]
        );
    }
}
