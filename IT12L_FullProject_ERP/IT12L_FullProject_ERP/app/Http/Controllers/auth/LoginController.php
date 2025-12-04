<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        // Attempt to log the user in
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Get the authenticated user
            $user = Auth::user();

            // Check if user is admin
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Welcome back, Admin!');
            }

            // For regular users, redirect to home or intended page
            // The cart sync will be handled by JavaScript on the frontend
            return redirect()->intended(route('home'))
                ->with('success', 'Welcome back!');
        }

        // Authentication failed
        return redirect()->back()
            ->withErrors(['email' => 'Invalid credentials.'])
            ->withInput();
    }
}