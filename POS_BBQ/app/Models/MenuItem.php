<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;
use App\Traits\SyncsToSupabase;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes, SyncsToSupabase;

    protected $fillable = ['category_id', 'name', 'description', 'price', 'image', 'availability'];

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

    /**
     * Get the maximum quantity of this menu item that can be prepared based on inventory.
     * Returns a high number if no ingredients are tracked.
     */
    public function getMaxQuantityAttribute()
    {
        // Load ingredients if not already loaded
        if (!$this->relationLoaded('inventoryItems')) {
            $this->load('inventoryItems');
        }

        $ingredients = $this->inventoryItems;

        if ($ingredients->isEmpty()) {
            return 0; // Strictly return 0 if no ingredients are linked/tracked
        }

        $maxable = [];

        foreach ($ingredients as $ingredient) {
            $needed = $ingredient->pivot->quantity_needed;
            if ($needed > 0) {
                // Determine available quantity of the ingredient
                // Assuming simple quantity column on inventory. 
                // If branch specific logic is needed, it would be here, but Inventory model currently doesn't seem to enforce strict branch separation on 'quantity' column in the shared schema provided so far, 
                // although there is 'branch_id' on inventories table.
                // For now, using the global quantity.
                $available = $ingredient->quantity;
                $maxable[] = floor($available / $needed);
            }
        }

        return empty($maxable) ? 999999 : min($maxable);
    }
}
