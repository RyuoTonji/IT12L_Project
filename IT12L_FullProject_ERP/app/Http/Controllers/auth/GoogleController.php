<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect to Google for authentication.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google callback.
     */
    public function handleGoogleCallback()
    {
        try {
            Log::info('Google Login Callback started');
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Google User retrieved', ['email' => $googleUser->email, 'id' => $googleUser->id]);

            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                Log::info('Existing user found', ['user_id' => $user->id]);
                // Update user with google_id if missing
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                    ]);
                }
                Auth::login($user);
            } else {
                Log::info('Creating new Google user');
                // Create a new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => encrypt('welcome123'), // Random placeholder
                    'is_admin' => false,
                ]);

                Auth::login($user);
            }

            Log::info('Google Login successful, redirecting to home');
            return redirect()->route('home');
            
        } catch (Exception $e) {
            Log::error('Google Login Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('login')->with('error', 'Google login failed. ' . $e->getMessage());
        }
    }
}
