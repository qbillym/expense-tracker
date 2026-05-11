<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): Model|null
    {
        if ($this->subject_type && $this->subject_id) {
            return $this->subject_type::find($this->subject_id);
        }
        return null;
    }

    /**
     * Log an activity
     */
    public static function log($userId, $action, $description = null, $subject = null, $oldValues = null, $newValues = null)
    {
        try {
            $data = [
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject ? $subject->id : null,
                'old_values' => $oldValues,
                'new_values' => $newValues,
            ];

            // Safely get request context
            try {
                $data['ip_address'] = request()->ip();
            } catch (\Exception $e) {
                $data['ip_address'] = null;
            }

            try {
                $data['user_agent'] = request()->userAgent();
            } catch (\Exception $e) {
                $data['user_agent'] = null;
            }

            return static::create($data);
        } catch (\Exception $e) {
            // Log the error but don't break the application
            \Log::error('ActivityLog failed: ' . $e->getMessage());
            return null;
        }
    }
}
