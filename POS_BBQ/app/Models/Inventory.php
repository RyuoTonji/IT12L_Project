<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes;

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
        return $this->belongsToMany(MenuItem::class, 'menu_item_ingredients')
            ->withPivot('quantity_needed', 'unit')
            ->withTimestamps();
    }
}
