<?php

namespace App\Listeners;

use App\Models\Cart;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class MergeGuestCart
{
    public function handle(Login $event)
    {
        try {
            $user = $event->user;
            $sessionId = session()->getId();
            
            Log::info('Login detected - attempting cart merge', [
                'user_id' => $user->id,
                'session_id' => $sessionId
            ]);
            
            // Migrate guest cart to user cart
            Cart::migrateToUser($sessionId, $user->id);
            
            // Regenerate session for security
            session()->regenerate();
            
            Log::info('Cart merged successfully', [
                'user_id' => $user->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error merging cart on login', [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id
            ]);
        }
    }
}