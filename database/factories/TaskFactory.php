<?php

namespace Database\Factories;

use App\Models\Severity;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $status = Status::inRandomOrder()->first() ?? Status::create([
            'name' => fake()->unique()->word(),
            'is_active' => true,
            'color' => \App\Enums\StatusColor::cases()[array_rand(\App\Enums\StatusColor::cases())],
            'sort_order' => fake()->numberBetween(0, 100),
        ]);

        $severity = Severity::inRandomOrder()->first() ?? Severity::create([
            'name' => fake()->unique()->word(),
            'color' => \App\Enums\SeverityColor::cases()[array_rand(\App\Enums\SeverityColor::cases())],
            'sort_order' => fake()->numberBetween(0, 100),
        ]);

        $developer = User::inRandomOrder()->first() ?? User::factory()->create([
            'role' => 'developer',
        ]);

        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraphs(5, true),
            'status_id' => $status->id,
            'severity_id' => $severity->id,
            'developer_id' => $developer->id,
            'start_date' => fake()->date(),
            'due_date' => fake()->date(),
            'finish_date' => null,
            'created_by' => $developer->id,
        ];
    }
}
