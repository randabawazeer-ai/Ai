<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'expense_reminder_enabled',
        'expense_reminder_time',
        'budget_alert_enabled',
        'budget_alert_threshold',
    ];

    protected function casts(): array
    {
        return [
            'expense_reminder_enabled' => 'boolean',
            'budget_alert_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
