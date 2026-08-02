<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if ($user->tenant && !$user->tenant->is_active) {
            return redirect()->route('tenant-inactive')
                ->with('error', 'Your tenant account is currently inactive. Please contact the administrator.');
        }

        return $next($request);
    }
}
