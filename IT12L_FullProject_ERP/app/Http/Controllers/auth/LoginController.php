<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = '/';
    protected $maxAttempts = 5;
    protected $decayMinutes = 1;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ✅ FIXED: Proper session data preservation through regeneration
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // ✅ STEP 1: Capture OLD session ID BEFORE login
        $oldSessionId = $request->session()->getId();
        
        Log::info('Login attempt', ['email' => $request->input('email')]);

        if ($this->attemptLogin($request)) {
            // ✅ STEP 2: Login successful
            Log::info('🟢 STEP 2: Login successful', [
                'old_session' => $oldSessionId,
                'user_id' => Auth::id()
            ]);
            
            // ✅ CRITICAL FIX: Store in regular session BEFORE regeneration
            // Don't use flash() - it can be lost during regenerate()
            $request->session()->put('_temp_old_session_id', $oldSessionId);
            
            Log::info('🟡 STEP 3: Temp session data saved', [
                'temp_old_session' => session('_temp_old_session_id'),
                'current_session' => $request->session()->getId()
            ]);

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        return Auth::attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }

    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    /**
     * ✅ FIXED: Proper session data preservation through regeneration
     */
    protected function sendLoginResponse(Request $request)
    {
        // ✅ STEP 4: Get old session ID from temp storage BEFORE regeneration
        $oldSessionId = $request->session()->get('_temp_old_session_id');
        
        if (!$oldSessionId) {
            Log::error('❌ STEP 4 FAILED: Old session ID not found in temp storage!', [
                'all_session_keys' => array_keys(session()->all())
            ]);
            // Don't fail - just proceed without migration
            $oldSessionId = null;
        } else {
            Log::info('🔵 STEP 4: Retrieved old session from temp storage', [
                'old_session' => $oldSessionId,
                'before_regeneration' => true
            ]);
        }
        
        // ✅ STEP 5: Regenerate session (for security)
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        // ✅ STEP 6: Get NEW session ID after regeneration
        $newSessionId = $request->session()->getId();
        
        Log::info('🟢 STEP 5-6: After session regeneration', [
            'new_session_id' => $newSessionId,
            'old_session_preserved' => $oldSessionId !== null
        ]);
        
        // ✅ STEP 7: Now store migration data in the NEW session
        if ($oldSessionId && $oldSessionId !== $newSessionId) {
            $request->session()->put('_cart_old_session_id', $oldSessionId);
            $request->session()->put('_cart_new_session_id', $newSessionId);
            $request->session()->put('_cart_migration_needed', true);
            
            // ✅ CRITICAL: Save the session immediately to ensure data persists
            $request->session()->save();
            
            Log::info('🟡 STEP 7: Migration data stored in new session', [
                'stored_old_session' => session('_cart_old_session_id'),
                'stored_new_session' => session('_cart_new_session_id'),
                'stored_migration_flag' => session('_cart_migration_needed'),
                'session_saved' => true
            ]);
        } else {
            Log::warning('⚠️ STEP 7: Skipped migration (same session or no old session)', [
                'old_session' => $oldSessionId,
                'new_session' => $newSessionId
            ]);
        }
        
        // ✅ STEP 8: Clean up temp storage
        $request->session()->forget('_temp_old_session_id');

        if ($response = $this->authenticated($request, Auth::user())) {
            return $response;
        }

        return $request->wantsJson()
                    ? new \Illuminate\Http\JsonResponse([], 204)
                    : redirect()->intended($this->redirectPath());
    }

    protected function authenticated(Request $request, $user)
    {
        Log::info('🟢 STEP 9: User authenticated - final verification', [
            'user_id' => $user->id,
            'email' => $user->email,
            'session_id' => session()->getId(),
            'migration_data' => [
                'old_session' => session('_cart_old_session_id'),
                'new_session' => session('_cart_new_session_id'),
                'migration_needed' => session('_cart_migration_needed')
            ],
            'all_session_keys' => array_keys(session()->all())
        ]);

        return redirect()->intended($this->redirectPath());
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    public function redirectPath()
    {
        if (request()->has('redirect')) {
            $redirect = request()->input('redirect');
            
            $redirectMap = [
                'checkout' => '/checkout',
                'cart' => '/cart',
                'orders' => '/orders',
            ];
            
            if (isset($redirectMap[$redirect])) {
                return $redirectMap[$redirect];
            }
        }
        
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        
        Log::info('User logging out', [
            'user_id' => $userId,
            'session_id' => $sessionId
        ]);

        Auth::logout();

        // Clean up all cart-related session data
        $request->session()->forget([
            '_cart_old_session_id', 
            '_cart_new_session_id', 
            '_cart_migration_needed',
            '_temp_old_session_id'
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out - session invalidated');

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect('/');
    }

    protected function guard()
    {
        return Auth::guard();
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return RateLimiter::tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts
        );
    }

    protected function incrementLoginAttempts(Request $request)
    {
        RateLimiter::hit(
            $this->throttleKey($request),
            $this->decayMinutes * 60
        );
    }

    protected function clearLoginAttempts(Request $request)
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    protected function fireLockoutEvent(Request $request)
    {
        event(new \Illuminate\Auth\Events\Lockout($request));
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => [trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(429);
    }

    protected function throttleKey(Request $request)
    {
        return Str::transliterate(
            Str::lower($request->input('email')) . '|' . $request->ip()
        );
    }

    public function username()
    {
        return 'email';
    }
}