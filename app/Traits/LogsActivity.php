<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Record an activity log entry.
     */
    public static function logActivity(
        string $action,
        string $description,
        ?self $subject = null,
        array $properties = []
    ): void {
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'description'  => $description,
            'subject_type' => $subject ? static::class : null,
            'subject_id'   => $subject?->id,
            'ip_address'   => request()->ip(),
            'properties'   => $properties ?: null,
        ]);
    }
}
