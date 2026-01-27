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
        $severities = Severity::all();
        $users = User::where('role', 'developer')->get();

        foreach (range(1, 20) as $i) {
            Task::factory()->create([
                'status_id' => $statuses->random()->id,
                'severity_id' => $severities->random()->id,
                'developer_id' => $users->random()->id,
                'created_by' => $users->random()->id,
            ]);
        }
    }
}
