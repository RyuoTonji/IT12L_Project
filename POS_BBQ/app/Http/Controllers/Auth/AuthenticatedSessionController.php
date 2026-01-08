<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // return redirect()->intended(route('dashboard', absolute: false));
        // Redirect based on user role
        if (Auth::user()->role === 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        } else {
            return redirect()->intended(route('cashier.dashboard', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Check if user role requires shift report
        // Check if user role requires shift report
        if (in_array($user->role, ['cashier', 'inventory', 'manager'])) {
            // Use the centralized logic to check if a report is actually needed
            // (i.e. if there was activity today and no report yet)
            if (\App\Http\Controllers\ShiftReportController::needsReport($user)) {
                $route = ($user->role === 'inventory') ? 'inventory.daily-report' : 'shift-reports.create';
                return redirect()->route($route)->with('error', 'You must submit your shift report before logging out.');
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
