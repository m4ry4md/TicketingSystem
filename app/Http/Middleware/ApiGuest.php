<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiGuest.
 *
 * This middleware ensures that the current user is not authenticated via the API guard.
 * It's typically used to protect routes like login or registration that should only
 * be accessible to unauthenticated users (guests).
 */
class ApiGuest
{
    /**
     * Handle an incoming request.
     *
     * Checks if the user is already logged in through the 'api' guard. If so,
     * it blocks the request with a 403 Forbidden response. Otherwise, it
     * allows the request to proceed to the next middleware in the stack.
     *
     * @param  \Illuminate\Http\Request  $request The incoming HTTP request.
     * @param  \Closure  $next A closure that represents the next middleware.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is authenticated with the 'api' guard, they are not a guest.
        if (Auth::guard('api')->check()) {
            // Block access for already authenticated users.
            return response()->json([
                'message' => __('auth.already_logged_in')
            ], 403); // 403 Forbidden
        }

        // If the user is not authenticated, allow the request to continue.
        return $next($request);
    }
}
