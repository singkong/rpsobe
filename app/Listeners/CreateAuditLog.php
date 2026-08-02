<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class CreateAuditLog
{
    public function handle(object $event): void
    {
        $rps = $event->rps;
        $actor = $event->actor;

        AuditLog::create([
            'user_id' => $actor->id,
            'tenant_id' => $actor->tenant_id,
            'action' => class_basename($event),
            'model_type' => get_class($rps),
            'model_id' => $rps->id,
            'new_values' => [
                'status' => $rps->status->value,
                'version_label' => $rps->version_label,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
