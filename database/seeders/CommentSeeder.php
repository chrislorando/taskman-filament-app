<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = \App\Models\Task::all();

        foreach ($tasks as $task) {
            $users = collect();
            $users->push(\App\Models\User::where('role', 'admin')->first());
            $users->push($task->developer);
            $users = $users->filter()->unique('id');

            foreach (range(1, rand(1, 5)) as $i) {
                Comment::factory()->create([
                    'task_id' => $task->id,
                    'user_id' => $users->random()->id,
                ]);
            }
        }
    }
}
