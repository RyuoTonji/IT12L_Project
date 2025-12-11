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
                $user = Auth::guard($guard)->user();

                $redirect = match ($user->role ?? '') {
                    'admin' => redirect()->route('admin.dashboard'),
                    'manager' => redirect()->route('manager.dashboard'),
                    'inventory' => redirect()->route('inventory.dashboard'),
                    'cashier' => redirect()->route('cashier.dashboard'),
                    default => function () use ($user, $guard) {
                            \Log::error('User login failed: Invalid or missing role', [
                            'user_id' => $user->id,
                            'role' => $user->role ?? 'null'
                            ]);
                            Auth::guard($guard)->logout();
                            abort(403, 'Access denied: No role assigned to your account or the account is not registered. Please contact administrator.');
                        }
                };

                return is_callable($redirect) ? $redirect() : $redirect;
            }
        }

        return $next($request);
    }
}
