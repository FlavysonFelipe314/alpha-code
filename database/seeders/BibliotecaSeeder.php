<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Biblioteca;

class BibliotecaSeeder extends Seeder
{
    public function run(): void
    {
        Biblioteca::factory()->count(8)->create();
    }
}
