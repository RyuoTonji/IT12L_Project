<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes;

    protected $fillable = ['category_id', 'name', 'description', 'price', 'image', 'is_available'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the ingredients for this menu item.
     */
    public function ingredients()
    {
        return $this->hasMany(MenuItemIngredient::class);
    }

    /**
     * Get inventory items used in this menu item.
     */
    /**
     * Get inventory items used in this menu item.
     */
    public function inventoryItems()
    {
        return $this->belongsToMany(Inventory::class, 'menu_item_ingredients')
            ->withPivot('quantity_needed', 'unit')
            ->withTimestamps();
    }

    /**
     * The branches that have this menu item.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'menu_item_branch')
            ->withPivot('is_available')
            ->withTimestamps();
    }

    /**
     * Check if the menu item is available in a specific branch.
     */
    public function isAvailableInBranch($branchId)
    {
        return $this->branches()
            ->where('branch_id', $branchId)
            ->wherePivot('is_available', true)
            ->exists();
    }
}
