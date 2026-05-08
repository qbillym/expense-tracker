<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Expense;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'target_amount',
        'budget_start_date',
        'budget_end_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'target_amount' => 'decimal:2',
            'budget_start_date' => 'date',
            'budget_end_date' => 'date',
        ];
    }

    public function budgetTargets(): HasMany
    {
        return $this->hasMany(BudgetTarget::class)->orderByDesc('start_date');
    }

    public function completedBudgetTargets(): HasMany
    {
        return $this->hasMany(BudgetTarget::class)
            ->whereNotNull('locked_at')
            ->orderByDesc('end_date');
    }

    public function refreshBudgetLocks(): void
    {
        $targets = $this->budgetTargets()->whereNull('locked_at')->get();
        foreach ($targets as $target) {
            $spent = $this->expenses()
                ->whereBetween('date', [$target->start_date->toDateString(), $target->end_date->toDateString()])
                ->sum('amount');

            // Lock if: target amount reached/exceeded OR end date is past
            if ($spent >= $target->target_amount || $target->end_date->isPast()) {
                $target->update(['locked_at' => now()]);
            }
        }
    }

    public function lockCurrentTarget(): void
    {
        $activeTarget = $this->budgetTargets()
            ->whereNull('locked_at')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderByDesc('start_date')
            ->first();

        if ($activeTarget && $activeTarget->id) {
            $activeTarget->update(['locked_at' => now()]);
        }
    }

    public function isBudgetLocked(): bool
    {
        $activeTarget = $this->activeBudgetTarget();
        if (!$activeTarget) {
            return false;
        }

        // Check if spending has reached or exceeded target
        $spent = $this->budgetSpent();
        return $spent >= $activeTarget->target_amount;
    }

    public function activeBudgetTarget(): ?BudgetTarget
    {
        $this->refreshBudgetLocks();

        $target = $this->budgetTargets()
            ->whereNull('locked_at')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderByDesc('start_date')
            ->first();

        if ($target) {
            return $target;
        }

        // Backward compatibility for users with legacy budget fields only.
        if ($this->target_amount !== null && $this->budget_end_date !== null && ($this->budget_end_date->isToday() || $this->budget_end_date->isFuture())) {
            $legacy = new BudgetTarget([
                'target_amount' => $this->target_amount,
                'start_date' => $this->budget_start_date ?? now()->startOfDay(),
                'end_date' => $this->budget_end_date,
            ]);
            $legacy->user_id = $this->id;

            return $legacy;
        }

        return null;
    }

    public function hasActiveBudget(): bool
    {
        return $this->activeBudgetTarget() !== null;
    }

    public function hasExpiredBudget(): bool
    {
        return $this->budgetTargets()
            ->where(function ($query) {
                $query->whereNotNull('locked_at')
                    ->orWhereDate('end_date', '<', now()->toDateString());
            })
            ->exists();
    }

    public function budgetDaysRemaining(): int
    {
        try {
            if (!$this->hasActiveBudget()) {
                return 0;
            }

            return now()->diffInDays($this->activeBudgetTarget()->end_date, false);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function budgetDaysElapsed(): int
    {
        try {
            $activeTarget = $this->activeBudgetTarget();
            if (!$activeTarget) {
                return 0;
            }

            return $activeTarget->start_date->diffInDays(now(), false);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function budgetTotalDays(): int
    {
        $activeTarget = $this->activeBudgetTarget();
        if (!$activeTarget) {
            return 0;
        }

        return $activeTarget->start_date->diffInDays($activeTarget->end_date) + 1;
    }

    public function isOverspending(): bool
    {
        try {
            if (!$this->hasActiveBudget()) {
                return false;
            }
            
            $activeTarget = $this->activeBudgetTarget();
            return $activeTarget ? $this->budgetSpent() > $activeTarget->target_amount : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function dailyAverageSpending(): float
    {
        $daysElapsed = $this->budgetDaysElapsed();
        if ($daysElapsed <= 0) {
            return 0;
        }
        
        return $this->budgetSpent() / $daysElapsed;
    }

    public function recommendedDailySpending(): float
    {
        if (!$this->hasActiveBudget()) {
            return 0;
        }
        
        $activeTarget = $this->activeBudgetTarget();
        return $activeTarget ? $activeTarget->target_amount / $this->budgetTotalDays() : 0;
    }

    public function budgetDurationLabel(): string
    {
        $activeTarget = $this->activeBudgetTarget();
        if (! $activeTarget) {
            return 'No active target';
        }

        return $activeTarget->start_date->format('M d') . ' - ' . $activeTarget->end_date->format('M d');
    }

    public function budgetPeriodExpenses()
    {
        try {
            $activeTarget = $this->activeBudgetTarget();
            if (! $activeTarget) {
                return collect();
            }

            return $this->expenses()
                ->whereBetween('date', [$activeTarget->start_date->toDateString(), $activeTarget->end_date->toDateString()])
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function budgetSpent(): float
    {
        try {
            return $this->budgetPeriodExpenses()->sum('amount');
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function budgetRemaining(): float
    {
        try {
            $activeTarget = $this->activeBudgetTarget();
            $targetAmount = $activeTarget ? (float) $activeTarget->target_amount : 0;
            return max(0, $targetAmount - $this->budgetSpent());
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function budgetProgressPercent(): int
    {
        try {
            $activeTarget = $this->activeBudgetTarget();
            if (! $activeTarget || $activeTarget->target_amount <= 0) {
                return 0;
            }

            return min(100, (int) round(($this->budgetSpent() / $activeTarget->target_amount) * 100));
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function budgetTopCategory(): ?string
    {
        try {
            $categoryTotals = $this->budgetPeriodExpenses()->groupBy('category')->map(function ($group) {
                return $group->sum('amount');
            });

            if ($categoryTotals->isEmpty()) {
                return null;
            }

            return $categoryTotals->sortDesc()->keys()->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->orderByDesc('created_at');
    }
}
