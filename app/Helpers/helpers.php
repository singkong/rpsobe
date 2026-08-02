<?php

if (!function_exists('current_tenant')) {
    function current_tenant(): ?\App\Models\Tenant
    {
        if (auth()->check()) {
            return auth()->user()->tenant;
        }

        return null;
    }
}

if (!function_exists('current_tenant_id')) {
    function current_tenant_id(): ?int
    {
        if (auth()->check()) {
            return auth()->user()->tenant_id;
        }

        return null;
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }
}

if (!function_exists('format_rupiah')) {
    function format_rupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('user_has_role')) {
    function user_has_role(string $role): bool
    {
        return auth()->check() && auth()->user()->hasRole($role);
    }
}
