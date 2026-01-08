<?php

namespace App\Models;

use App\Traits\SyncsToSupabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

/**
 * App\Models\OrderItem
 *
 * @property int $id
 * @property int $order_id
 * @property int $menu_item_id
 * @property int $quantity
 * @property float $unit_price
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\MenuItem $menuItem
 */
class OrderItem extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes, SyncsToSupabase;

    protected $table = 'pos_order_items';

    protected $fillable = ['order_id', 'menu_item_id', 'quantity', 'unit_price', 'notes'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
