<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Cart extends Model
{
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

    // Static helper to get or create cart
    public static function getOrCreate($userId = null, $sessionId = null)
    {
        if ($userId) {
            return static::firstOrCreate(['user_id' => $userId]);
        }
        
        return static::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * ENHANCED: Merge guest cart to user cart with intelligent handling
     * This merges quantities for duplicate items
     * THIS METHOD IS ALREADY CORRECT - NO CHANGES NEEDED
     */
    public static function mergeGuestCartToUser($sessionId, $userId)
    {
        if (!$sessionId || !$userId) {
            Log::warning('Cart merge skipped: missing sessionId or userId', [
                'sessionId' => $sessionId,
                'userId' => $userId
            ]);
            return null;
        }

        DB::beginTransaction();

        try {
            // Find guest cart
            $guestCart = static::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->with('items')
                ->first();

            if (!$guestCart || $guestCart->items->isEmpty()) {
                Log::info('No guest cart to merge', ['sessionId' => $sessionId]);
                DB::commit();
                return null;
            }

            // Get or create user cart
            $userCart = static::getOrCreate($userId);

            Log::info('Merging guest cart to user cart', [
                'sessionId' => $sessionId,
                'userId' => $userId,
                'guestItems' => $guestCart->items->count()
            ]);

            // Transfer items with intelligent merging
            $mergedCount = 0;
            foreach ($guestCart->items as $guestItem) {
                $userCart->addItem($guestItem->product_id, $guestItem->quantity);
                $mergedCount++;
            }

            // Delete guest cart and its items
            $guestCart->items()->delete();
            $guestCart->delete();

            DB::commit();

            Log::info('Cart merge completed successfully', [
                'userId' => $userId,
                'itemsMerged' => $mergedCount
            ]);

            return $userCart;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cart merge failed', [
                'sessionId' => $sessionId,
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * LEGACY: Keep old method for backward compatibility
     */
    public static function migrateToUser($sessionId, $userId)
    {
        return static::mergeGuestCartToUser($sessionId, $userId);
    }
}