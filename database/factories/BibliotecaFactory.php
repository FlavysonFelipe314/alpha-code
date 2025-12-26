<?php

namespace Database\Factories;

use App\Models\Biblioteca;
use Illuminate\Database\Eloquent\Factories\Factory;

class BibliotecaFactory extends Factory
{
    protected $model = Biblioteca::class;

    public function definition()
    {
        $types = ['book','video','audio'];
        $statuses = ['in-progress','completed','wishlist'];
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'type' => $this->faker->randomElement($types),
            'status' => $this->faker->randomElement($statuses),
            'progress' => $this->faker->numberBetween(0,100),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
