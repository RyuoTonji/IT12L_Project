<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showPhoneLoginForm()
    {
        return view('auth.login-phone');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Detect if input is email or phone number
        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'contact_number';

        $credentials = [
            $field    => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/menu'); // Change to your home page
        }

        // Check if user actually exists
        $userExists = User::where('email', $request->email)
                          ->orWhere('contact_number', $request->email)
                          ->exists();

        if (!$userExists) {
            return back()->withErrors([
                'email_not_found' => 'These credentials do not match our records.'
            ])->withInput();
        }

        return back()->withErrors([
            'wrong_password' => 'The password you’ve entered is incorrect'
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}