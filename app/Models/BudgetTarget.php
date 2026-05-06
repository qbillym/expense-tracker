<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_amount',
        'start_date',
        'end_date',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'locked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->locked_at === null
            && ($this->end_date->isToday() || $this->end_date->isFuture());
    }
}
