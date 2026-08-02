<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            if ($model instanceof AuditLog) {
                return;
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'tenant_id' => auth()->user()?->tenant_id,
                'action' => 'created',
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'new_values' => $model->getOriginal(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });

        static::updated(function ($model) {
            if ($model instanceof AuditLog) {
                return;
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'tenant_id' => auth()->user()?->tenant_id,
                'action' => 'updated',
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'old_values' => $model->getOriginal(),
                'new_values' => $model->getChanges(),
                'changes' => array_diff_assoc($model->getChanges(), $model->getOriginal()),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });

        static::deleted(function ($model) {
            if ($model instanceof AuditLog) {
                return;
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'tenant_id' => auth()->user()?->tenant_id,
                'action' => 'deleted',
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'old_values' => $model->getOriginal(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });
    }
}
