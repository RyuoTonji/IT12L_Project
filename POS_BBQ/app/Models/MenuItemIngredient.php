<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SyncsToSupabase;

class MenuItemIngredient extends Model
{
    use HasFactory, SyncsToSupabase;
    protected $table = 'pos_menu_item_ingredients';

    protected $fillable = [
        'menu_item_id',
        'inventory_id',
        'quantity_needed',
        'unit',
    ];

    protected $casts = [
        'quantity_needed' => 'decimal:2',
    ];

    /**
     * Get the menu item that owns this ingredient relationship.
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get the inventory item for this ingredient.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
