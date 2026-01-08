<?php

namespace App\Models;

use App\Traits\SyncsToSupabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

/**
 * App\Models\Inventory
 *
 * @property int $id
 * @property string $name
 * @property string|null $supplier
 * @property string|null $category
 * @property float $quantity
 * @property float $sold
 * @property float $spoilage
 * @property float $stock_in
 * @property float $stock_out
 * @property string|null $unit
 * @property float $reorder_level
 * @property int $branch_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MenuItem[] $menuItems
 */
class Inventory extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes, SyncsToSupabase;
    protected $table = 'pos_inventory';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);
    }

    protected $fillable = [
        'name',
        'supplier',
        'category',
        'quantity',
        'sold',
        'spoilage',
        'stock_in',
        'stock_out',
        'unit',
        'reorder_level',
        'branch_id',
    ];

    /**
     * Get the branch that owns the inventory.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the menu items that use this inventory item.
     */
    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'pos_menu_item_ingredients')
            ->withPivot('quantity_needed', 'unit')
            ->withTimestamps();
    }
}
