<?php

namespace Database\Factories;

use App\Models\Objetivo;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObjetivoFactory extends Factory
{
    protected $model = Objetivo::class;

    public function definition()
    {
        $topics = ['saude','financas','carreira','pessoal','outros'];
        $reminders = [];
        $count = $this->faker->numberBetween(0,4);
        for ($i=0;$i<$count;$i++) {
            $reminders[] = ['id' => $this->faker->unique()->numberBetween(1000,9999),'text' => $this->faker->sentence(4),'completed' => $this->faker->boolean(40)];
        }

        return [
            'title' => $this->faker->sentence(4),
            'topic' => $this->faker->randomElement($topics),
            'description' => $this->faker->optional()->paragraph(),
            'deadline' => $this->faker->dateTimeBetween('now','+6 months')->format('Y-m-d'),
            'completed' => $this->faker->boolean(20),
            'reminders' => $reminders,
        ];
    }
}
