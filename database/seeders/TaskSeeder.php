<?php

namespace Database\Seeders;

use App\Models\Severity;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = Status::all();
        $completedStatus = Status::where('name', 'Completed')->first();
        $severities = Severity::all();
        $developers = User::where('role', 'developer')->get();
        $allUsers = User::all();

        foreach (range(1, 20) as $i) {
            $status = $statuses->random();
            $finishDate = null;

            if ($status->id === $completedStatus?->id) {
                $finishDate = fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d');
            }

            Task::factory()->create([
                'status_id' => $status->id,
                'severity_id' => $severities->random()->id,
                'developer_id' => $developers->random()->id,
                'created_by' => $allUsers->random()->id,
                'finish_date' => $finishDate,
            ]);
        }
    }
}
