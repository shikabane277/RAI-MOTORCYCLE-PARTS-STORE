<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // If specific roles required, check them
        if (!empty($roles)) {
            if (!in_array($user->role, $roles)) {
                abort(403, 'Access denied.');
            }
        } else {
            // Default: any staff role
            if (!$user->isStaff()) {
                abort(403, 'Access denied.');
            }
        }

        return $next($request);
    }
}
