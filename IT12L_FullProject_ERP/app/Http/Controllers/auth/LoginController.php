<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // *** IMPORTANT: Capture session ID BEFORE authentication ***
        // Session ID changes after login, so we need the old one
        $oldSessionId = Session::getId();

        // Attempt to log the user in
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session for security
            $request->session()->regenerate();

            // Get the authenticated user
            $user = Auth::user();

            // *** CART TRANSFER LOGIC ***
            // Merge guest cart (from old session) to user cart
            if ($oldSessionId) {
                try {
                    Cart::mergeGuestCartToUser($oldSessionId, $user->id);
                    Log::info('Cart merged after login', [
                        'userId' => $user->id,
                        'sessionId' => $oldSessionId
                    ]);
                } catch (\Exception $e) {
                    Log::error('Cart merge failed during login', [
                        'userId' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail login if cart merge fails
                }
            }

            // Check if user is admin
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Welcome back, Admin!');
            }

            // For regular users, redirect to home or intended page
            return redirect()->intended(route('home'))
                ->with('success', 'Welcome back!')
                ->with('cart_merged', true); // Signal frontend to sync
        }

        // Authentication failed
        return redirect()->back()
            ->withErrors(['email' => 'Invalid credentials.'])
            ->withInput();
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}