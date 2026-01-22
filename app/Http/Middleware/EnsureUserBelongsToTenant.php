<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToTenant
{
    /**
     * Handle an incoming request.
     * Allow any authenticated user that belongs to a tenant (employee, tenant_admin, or super_admin)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // User must have a tenant_id (belongs to a company)
        if (!$user->tenant_id && !$user->isSuperAdmin()) {
            abort(403, 'No perteneces a ninguna empresa.');
        }

        return $next($request);
    }
}
