<?php

namespace App\Filament\Resources\SeverityResource\Pages;

use App\Filament\Resources\SeverityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\MaxWidth;

class ManageSeverities extends ManageRecords
{
    protected static string $resource = SeverityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth(MaxWidth::Large),
        ];
    }
}
