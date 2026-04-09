<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        array $meta = []
    ): void {
        try {
            AuditLog::query()->create([
                'user_id' => Auth::id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => request()?->ip(),
                'meta' => empty($meta) ? null : $meta,
            ]);
        } catch (QueryException) {
            // Skip logging when migration is not yet applied.
        }
    }
}

