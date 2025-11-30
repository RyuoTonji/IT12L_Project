<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is an admin
        if (Auth::check() && Auth::user()->is_admin) {
            // If it's an AJAX request, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Administrators cannot add items to cart.'
                ], 403);
            }
            
            // For regular requests, redirect back with error
            return redirect()->back()->with('error', 'Administrators cannot add items to cart.');
        }

        return $next($request);
    }
}