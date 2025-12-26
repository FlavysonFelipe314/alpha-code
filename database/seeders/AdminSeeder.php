<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin padrão
        User::firstOrCreate(
            ['email' => 'admin@alphacode.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@alphacode.com',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'theme_colors' => [
                    'primary' => '#ef4444',
                    'secondary' => '#dc2626',
                ],
            ]
        );

        $this->command->info('Usuário admin criado!');
        $this->command->info('Email: admin@alphacode.com');
        $this->command->info('Senha: admin123');
        $this->command->warn('IMPORTANTE: Altere a senha após o primeiro login!');
    }
}