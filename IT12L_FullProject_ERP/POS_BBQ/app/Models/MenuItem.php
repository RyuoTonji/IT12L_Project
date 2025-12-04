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
    public function inventoryItems()
    {
        return $this->belongsToMany(Inventory::class, 'menu_item_ingredients')
            ->withPivot('quantity_needed', 'unit')
            ->withTimestamps();
    }
}
