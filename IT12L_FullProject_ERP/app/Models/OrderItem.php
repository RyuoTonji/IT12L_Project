<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderItem
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property string $product_name
 * @property string|null $product_image
 * @property int $quantity
 * @property float $unit_price
 * @property int|null $menu_item_id
 * @property float $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Order $order
 */
class OrderItem extends Model
{
    protected $table = 'crm_order_items';
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'quantity',
        'unit_price',
        'menu_item_id',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}