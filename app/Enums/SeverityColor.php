<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SeverityColor: string implements HasColor, HasLabel
{
    case Danger = 'danger';
    case Gray = 'gray';
    case Info = 'info';
    case Primary = 'primary';
    case Success = 'success';
    case Warning = 'warning';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Danger => 'Danger',
            self::Gray => 'Gray',
            self::Info => 'Info',
            self::Primary => 'Primary',
            self::Success => 'Success',
            self::Warning => 'Warning',
        };
    }

    public function getColor(): string|array|null
    {
        return $this->value;
    }
}
