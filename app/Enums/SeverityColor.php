<?php

namespace App\Enums;

enum SeverityColor: string
{
    case Danger = 'danger';
    case Gray = 'gray';
    case Info = 'info';
    case Primary = 'primary';
    case Success = 'success';
    case Warning = 'warning';
}
