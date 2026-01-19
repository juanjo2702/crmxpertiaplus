<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTenantAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow both tenant_admin and super_admin to access tenant routes
        if (!$user->isTenantAdmin() && !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Tenant Admin access required.');
        }

        return $next($request);
    }
}
