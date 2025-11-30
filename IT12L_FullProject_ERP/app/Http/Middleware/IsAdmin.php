<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            // Redirect non-admin users
            return redirect()->route('home')->with('error', 'Access denied. Admin only.');
        }

        return $next($request);
    }
}