<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\RPS;
use Illuminate\Support\Facades\Request;

class RPSObserver
{
    public function updated(RPS $rps): void
    {
        if ($rps->wasChanged('status')) {
            $oldStatus = $rps->getOriginal('status');
            $newStatus = $rps->status->value;

            $statusLabel = $rps->status->label();

            AuditLog::create([
                'user_id' => auth()->id(),
                'tenant_id' => auth()->user()?->tenant_id,
                'action' => 'status_changed',
                'model_type' => RPS::class,
                'model_id' => $rps->id,
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => $newStatus],
                'changes' => [
                    'message' => "Status RPS berubah menjadi: {$statusLabel}",
                    'from' => $oldStatus,
                    'to' => $newStatus,
                ],
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }
}
