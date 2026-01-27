<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        $task = Task::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();

        return [
            'task_id' => $task ? $task->id : Task::factory(),
            'user_id' => $user ? $user->id : User::factory(),
            'parent_id' => null,
            'body' => fake()->text(),
        ];
    }
}
