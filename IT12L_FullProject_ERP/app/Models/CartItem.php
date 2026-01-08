<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'crm_cart_items';
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    public $timestamps = false;

    // Relationships
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->product->price;
    }
}