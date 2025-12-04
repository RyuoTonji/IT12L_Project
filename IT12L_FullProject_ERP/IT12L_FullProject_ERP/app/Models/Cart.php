<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // Migrate guest cart to user cart
    public static function migrateToUser($sessionId, $userId)
    {
        $guestCart = static::where('session_id', $sessionId)->first();
        
        if (!$guestCart) {
            return null;
        }

        $userCart = static::getOrCreate($userId);

        // Transfer items
        foreach ($guestCart->items as $item) {
            $userCart->addItem($item->product_id, $item->quantity);
        }

        // Delete guest cart
        $guestCart->delete();

        return $userCart;
    }
}