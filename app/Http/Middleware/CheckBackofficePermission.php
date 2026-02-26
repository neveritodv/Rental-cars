<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBackofficePermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Check if user is authenticated with backoffice guard
        if (!auth()->guard('backoffice')->check()) {
            abort(403, 'Non authentifié.');
        }

        $user = auth()->guard('backoffice')->user();
        
        // Check if user has the required permission
        if (!$user->can($permission)) {
            abort(403, "Vous n'avez pas la permission: {$permission}");
        }

        return $next($request);
    }
}