<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasIcon, HasLabel
{
    case Admin = 'admin';
    case Developer = 'developer';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Developer => 'Developer',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Developer => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Admin => 'heroicon-m-shield-check',
            self::Developer => 'heroicon-m-code-bracket',
        };
    }
}
