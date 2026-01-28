<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusColor: string implements HasColor, HasLabel
{
    case Gray = 'gray';
    case Info = 'info';
    case Warning = 'warning';
    case Success = 'success';
    case Danger = 'danger';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Gray => 'Gray',
            self::Info => 'Info',
            self::Warning => 'Warning',
            self::Success => 'Success',
            self::Danger => 'Danger',
        };
    }

    public function getColor(): string|array|null
    {
        return $this->value;
    }
}
