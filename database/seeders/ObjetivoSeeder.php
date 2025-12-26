<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Objetivo;

class ObjetivoSeeder extends Seeder
{
    public function run(): void
    {
        Objetivo::factory()->count(6)->create();
    }
}
