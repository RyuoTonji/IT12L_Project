<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Cart extends Model
{
    protected $table = 'crm_carts';
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    public $timestamps = false;

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Helper methods
    public function getTotal()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }

    public function getItemCount()
    {
        return $this->items->sum('quantity');
    }

    public function addItem($productId, $quantity = 1)
    {
        $existingItem = $this->items()->where('product_id', $productId)->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            return $existingItem;
        }

        return $this->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function updateItem($productId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }

        $item = $this->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->update(['quantity' => $quantity]);
            return $item;
        }

        return false;
    }

    public function removeItem($productId)
    {
        return $this->items()->where('product_id', $productId)->delete();
    }

    public function clear()
    {
        return $this->items()->delete();
    }

    public function getBranches()
    {
        return $this->items()
            ->with('product.branch')
            ->get()
            ->pluck('product.branch')
            ->unique('id');
    }

    /**
     * ✅ FIXED: Session-based cart with proper conflict handling
     * Get or create cart based on session_id (works for both guest and logged-in)
     * 
     * Logic:
     * 1. Try to find cart by session_id (current session)
     * 2. If user is logged in and cart not found, check for existing user cart
     * 3. Merge or update as needed to avoid duplicates
     */
    public static function getOrCreate($userId = null, $sessionId = null)
    {
        if (!$sessionId) {
            Log::warning('Cart::getOrCreate called without session_id', [
                'user_id' => $userId
            ]);
            return null;
        }

        Log::info('Cart::getOrCreate called', [
            'user_id' => $userId ?? 'guest',
            'session_id' => $sessionId
        ]);

        // PRIORITY 1: Find cart by session_id (current session)
        $cart = static::where('session_id', $sessionId)->first();

        if ($cart) {
            Log::info('Found cart by session_id', [
                'cart_id' => $cart->id,
                'has_user_id' => $cart->user_id ? 'yes' : 'no'
            ]);

            // If user just logged in and cart doesn't have user_id, link it
            if ($userId && !$cart->user_id) {
                // Check if this user already has a cart from another session
                $existingUserCart = static::where('user_id', $userId)
                    ->where('id', '!=', $cart->id)
                    ->first();

                if ($existingUserCart) {
                    // User has an old cart - merge items and delete old cart
                    Log::info('Found old cart for user, merging items', [
                        'old_cart_id' => $existingUserCart->id,
                        'current_cart_id' => $cart->id
                    ]);

                    static::mergeCartItems($existingUserCart, $cart);
                    $existingUserCart->delete();
                }

                // Link current cart to user
                $cart->update(['user_id' => $userId]);

                Log::info('Cart linked to user after login', [
                    'cart_id' => $cart->id,
                    'user_id' => $userId,
                    'session_id' => $sessionId
                ]);
            }

            return $cart;
        }

        // PRIORITY 2: If logged in, check for existing user cart (from old session)
        if ($userId) {
            $userCart = static::where('user_id', $userId)->first();

            if ($userCart) {
                // Update session_id to current session
                $userCart->update(['session_id' => $sessionId]);

                Log::info('Found existing user cart, updated session_id', [
                    'cart_id' => $userCart->id,
                    'user_id' => $userId,
                    'old_session' => $userCart->session_id,
                    'new_session' => $sessionId
                ]);

                return $userCart;
            }
        }

        // PRIORITY 3: Create new cart for this session
        try {
            $cart = static::create([
                'session_id' => $sessionId,
                'user_id' => $userId
            ]);

            Log::info('New cart created', [
                'cart_id' => $cart->id,
                'user_id' => $userId ?? 'guest',
                'session_id' => $sessionId
            ]);

            return $cart;

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate entry error (race condition)
            if ($e->getCode() == 23000) {
                Log::warning('Duplicate cart detected (race condition), fetching existing', [
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'error' => $e->getMessage()
                ]);

                // Try to fetch the existing cart
                if ($userId) {
                    $existingCart = static::where('user_id', $userId)->first();
                    if ($existingCart) {
                        $existingCart->update(['session_id' => $sessionId]);
                        return $existingCart;
                    }
                }

                if ($sessionId) {
                    $existingCart = static::where('session_id', $sessionId)->first();
                    if ($existingCart) {
                        return $existingCart;
                    }
                }
            }

            // Re-throw if we couldn't handle it
            throw $e;
        }
    }

    /**
     * Merge items from source cart to destination cart
     * Used when user logs in and has carts in multiple sessions
     */
    protected static function mergeCartItems($sourceCart, $destinationCart)
    {
        foreach ($sourceCart->items as $sourceItem) {
            $existingItem = $destinationCart->items()
                ->where('product_id', $sourceItem->product_id)
                ->first();

            if ($existingItem) {
                // Item exists - add quantities
                $existingItem->increment('quantity', $sourceItem->quantity);
                Log::info('Merged cart item quantity', [
                    'product_id' => $sourceItem->product_id,
                    'old_qty' => $existingItem->quantity - $sourceItem->quantity,
                    'added_qty' => $sourceItem->quantity,
                    'new_qty' => $existingItem->quantity
                ]);
            } else {
                // Item doesn't exist - copy it
                $destinationCart->items()->create([
                    'product_id' => $sourceItem->product_id,
                    'quantity' => $sourceItem->quantity,
                ]);
                Log::info('Copied cart item to destination', [
                    'product_id' => $sourceItem->product_id,
                    'quantity' => $sourceItem->quantity
                ]);
            }
        }
    }

    /**
     * ⚠️ DEPRECATED: No longer needed with session-based approach
     * Keeping for backward compatibility only
     */
    public static function mergeGuestCartToUser($sessionId, $userId)
    {
        Log::warning('mergeGuestCartToUser called but is DEPRECATED with session-based cart', [
            'sessionId' => $sessionId,
            'userId' => $userId
        ]);

        // With session-based cart, no merge is needed
        // The cart automatically persists through login
        return static::getOrCreate($userId, $sessionId);
    }

    /**
     * ⚠️ DEPRECATED: Alias for backward compatibility
     */
    public static function migrateToUser($sessionId, $userId)
    {
        return static::mergeGuestCartToUser($sessionId, $userId);
    }
}