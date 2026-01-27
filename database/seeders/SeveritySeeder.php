<?php

namespace Database\Seeders;

use App\Enums\SeverityColor;
use App\Models\Severity;
use Illuminate\Database\Seeder;

class SeveritySeeder extends Seeder
{
    public function run(): void
    {
        Severity::create([
            'name' => 'Low',
            'color' => SeverityColor::Success->value,
            'sort_order' => 1,
        ]);

        Severity::create([
            'name' => 'Medium',
            'color' => SeverityColor::Warning->value,
            'sort_order' => 2,
        ]);

        Severity::create([
            'name' => 'High',
            'color' => SeverityColor::Danger->value,
            'sort_order' => 3,
        ]);

        Severity::create([
            'name' => 'Info',
            'color' => SeverityColor::Info->value,
            'sort_order' => 5,
        ]);

        Severity::create([
            'name' => 'Trivial',
            'color' => SeverityColor::Gray->value,
            'sort_order' => 6,
        ]);
    }
}
