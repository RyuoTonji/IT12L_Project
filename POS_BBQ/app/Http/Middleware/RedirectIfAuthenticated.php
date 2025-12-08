<?php

namespace App\Http\Middleware;

// use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect based on user role
                $user = Auth::guard($guard)->user();

                return match ($user->role) {
                    'admin' => redirect()->route('admin.dashboard'),
                    'manager' => redirect()->route('manager.dashboard'),
                    'inventory' => redirect()->route('inventory.dashboard'),
                    'cashier' => redirect()->route('cashier.dashboard'),
                    default => redirect()->route('cashier.dashboard'),
                };
            }
        }

        return $next($request);
    }
}
