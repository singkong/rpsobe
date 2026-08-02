<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && auth()->check() && !auth()->user()->isSuperAdmin()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function (Builder $query) {
            if (auth()->check() && !auth()->user()->isSuperAdmin()) {
                $query->where($query->getModel()->getTable() . '.tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function scopeByTenant(Builder $query, $tenantId = null): void
    {
        $tenantId = $tenantId ?? auth()->user()?->tenant_id;
        if ($tenantId) {
            $query->where($query->getModel()->getTable() . '.tenant_id', $tenantId);
        }
    }

    public function scopeForCurrentTenant(Builder $query): void
    {
        if (auth()->check() && auth()->user()?->tenant_id) {
            $query->where($query->getModel()->getTable() . '.tenant_id', auth()->user()->tenant_id);
        }
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
