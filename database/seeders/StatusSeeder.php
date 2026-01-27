<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        Status::create([
            'name' => 'Waiting',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Status::create([
            'name' => 'In Progress',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Status::create([
            'name' => 'Pending',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Status::create([
            'name' => 'Completed',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        Status::create([
            'name' => 'Closed',
            'is_active' => true,
            'sort_order' => 5,
        ]);
    }
}
