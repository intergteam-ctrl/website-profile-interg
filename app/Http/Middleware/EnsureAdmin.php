<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards internal tools that live outside the Filament panel. Guests are
 * sent to the admin login and brought back afterwards; logged-in users
 * without the admin role are refused, matching User::canAccessPanel().
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest('/admin/login');
        }

        abort_unless($user->role === 'admin', 403);

        return $next($request);
    }
}
