<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register', [
            'page_title' => 'Register'
        ]);
    }

    /**
     * Proper session data preservation through regeneration (matching LoginController)
     */
    public function register(Request $request)
    {
        $this->validateRegistration($request);

        //  STEP 1: Capture OLD session ID BEFORE registration/login
        $oldSessionId = $request->session()->getId();
        
        Log::info('Registration attempt', [
            'email' => $request->input('email'),
            'old_session' => $oldSessionId
        ]);

        try {
            //  STEP 2: Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'is_admin' => false,
            ]);

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            //  STEP 3: Login the user
            Auth::login($user, $request->boolean('remember'));

            Log::info('🟢 STEP 3: User logged in after registration', [
                'old_session' => $oldSessionId,
                'user_id' => $user->id
            ]);

            //  CRITICAL FIX: Store in regular session BEFORE regeneration
            // Don't use flash() - it can be lost during regenerate()
            $request->session()->put('_temp_old_session_id', $oldSessionId);
            
            Log::info('🟡 STEP 4: Temp session data saved', [
                'temp_old_session' => session('_temp_old_session_id'),
                'current_session' => $request->session()->getId()
            ]);

            return $this->sendRegistrationResponse($request, $user);

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);

            throw ValidationException::withMessages([
                'email' => ['Registration failed. Please try again.'],
            ]);
        }
    }

    protected function validateRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     *  FIXED: Proper session data preservation through regeneration (matching LoginController)
     */
    protected function sendRegistrationResponse(Request $request, $user)
    {
        //  STEP 5: Get old session ID from temp storage BEFORE regeneration
        $oldSessionId = $request->session()->get('_temp_old_session_id');
        
        if (!$oldSessionId) {
            Log::error('❌ STEP 5 FAILED: Old session ID not found in temp storage!', [
                'all_session_keys' => array_keys(session()->all())
            ]);
            // Don't fail - just proceed without migration
            $oldSessionId = null;
        } else {
            Log::info('🔵 STEP 5: Retrieved old session from temp storage', [
                'old_session' => $oldSessionId,
                'before_regeneration' => true
            ]);
        }
        
        //  STEP 6: Regenerate session (for security)
        $request->session()->regenerate();

        //  STEP 7: Get NEW session ID after regeneration
        $newSessionId = $request->session()->getId();
        
        Log::info('🟢 STEP 6-7: After session regeneration', [
            'new_session_id' => $newSessionId,
            'old_session_preserved' => $oldSessionId !== null
        ]);
        
        //  STEP 8: Now store migration data in the NEW session
        if ($oldSessionId && $oldSessionId !== $newSessionId) {
            $request->session()->put('_cart_old_session_id', $oldSessionId);
            $request->session()->put('_cart_new_session_id', $newSessionId);
            $request->session()->put('_cart_migration_needed', true);
            
            // CRITICAL: Save the session immediately to ensure data persists
            $request->session()->save();
            
            Log::info('🟡 STEP 8: Migration data stored in new session', [
                'stored_old_session' => session('_cart_old_session_id'),
                'stored_new_session' => session('_cart_new_session_id'),
                'stored_migration_flag' => session('_cart_migration_needed'),
                'session_saved' => true
            ]);
        } else {
            Log::warning('⚠️ STEP 8: Skipped migration (same session or no old session)', [
                'old_session' => $oldSessionId,
                'new_session' => $newSessionId
            ]);
        }
        
        // STEP 9: Clean up temp storage
        $request->session()->forget('_temp_old_session_id');

        // STEP 10: Final verification
        Log::info('🟢 STEP 10: User registered - final verification', [
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

        // Handle redirect parameter
        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new \Illuminate\Http\JsonResponse([], 201)
                    : redirect()->intended($this->redirectPath())
                        ->with('success', 'Registration successful! Welcome, ' . $user->name . '!');
    }

    protected function registered(Request $request, $user)
    {
        // Hook for additional post-registration logic
        return redirect()->intended($this->redirectPath())
            ->with('success', 'Registration successful! Welcome, ' . $user->name . '!');
    }

    public function redirectPath()
    {
        // Check for redirect parameter (same as LoginController)
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
}