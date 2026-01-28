<?php

namespace Database\Seeders;

use App\Enums\StatusColor;
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
            'color' => StatusColor::Gray->value,
        ]);

        Status::create([
            'name' => 'In Progress',
            'is_active' => true,
            'sort_order' => 2,
            'color' => StatusColor::Info->value,
        ]);

        Status::create([
            'name' => 'Pending',
            'is_active' => true,
            'sort_order' => 3,
            'color' => StatusColor::Warning->value,
        ]);

        Status::create([
            'name' => 'Completed',
            'is_active' => true,
            'sort_order' => 4,
            'color' => StatusColor::Success->value,
        ]);

        Status::create([
            'name' => 'Closed',
            'is_active' => true,
            'sort_order' => 5,
            'color' => StatusColor::Danger->value,
        ]);
    }
}
