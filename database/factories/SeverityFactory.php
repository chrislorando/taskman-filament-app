<?php

namespace Database\Factories;

use App\Models\Severity;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeverityFactory extends Factory
{
    protected $model = Severity::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'color' => fake()->hexColor(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
