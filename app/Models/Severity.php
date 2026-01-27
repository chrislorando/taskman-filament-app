<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Severity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'color' => \App\Enums\SeverityColor::class,
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
