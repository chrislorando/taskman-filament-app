<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status_id',
        'severity_id',
        'developer_id',
        'start_date',
        'due_date',
        'finish_date',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'finish_date' => 'date',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function severity(): BelongsTo
    {
        return $this->belongsTo(Severity::class);
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
