<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Access Denied. Admins Only.');
        }

        return $next($request);
    }
}