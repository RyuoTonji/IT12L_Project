<?php

namespace App\Models;

use App\Traits\SyncsToSupabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InventoryAdjustment
 *
 * @property int $id
 * @property int $inventory_id
 * @property string $adjustment_type
 * @property float $quantity
 * @property float $quantity_before
 * @property float $quantity_after
 * @property string|null $reason
 * @property int|null $order_id
 * @property int|null $recorded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Inventory $inventory
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $recorder
 */
class InventoryAdjustment extends Model
{
    use HasFactory, SyncsToSupabase;
    protected $table = 'pos_inventory_adjustments';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);

        static::creating(function ($adjustment) {
            if (!$adjustment->branch_id && $adjustment->inventory) {
                $adjustment->branch_id = $adjustment->inventory->branch_id;
            }
        });
    }


    protected $fillable = [
        'branch_id',
        'inventory_id',
        'adjustment_type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reason',
        'order_id',
        'recorded_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
