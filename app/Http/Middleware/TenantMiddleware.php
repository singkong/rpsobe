<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
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

        if (!$user->tenant_id) {
            abort(403, 'You are not associated with any tenant.');
        }

        if (!$user->tenant || !$user->tenant->is_active) {
            abort(403, 'Your tenant is inactive. Please contact your administrator.');
        }

        $request->merge(['tenant_id' => $user->tenant_id]);

        return $next($request);
    }
}
