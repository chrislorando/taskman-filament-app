<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'is_active' => fake()->boolean(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
