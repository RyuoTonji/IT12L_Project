<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\SyncsToSupabase;

class Branch extends Model
{
    use HasFactory, SoftDeletes, SyncsToSupabase;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the orders for this branch.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the payments for this branch.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the inventory items for this branch.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get the shift reports for this branch.
     */
    public function shiftReports()
    {
        return $this->hasMany(ShiftReport::class);
    }

    /**
     * Get the menu items available in this branch.
     */
    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_branch')
            ->withPivot('is_available')
            ->withTimestamps();
    }

    /**
     * Get only available menu items for this branch.
     */
    public function availableMenuItems()
    {
        return $this->menuItems()->wherePivot('is_available', true);
    }
}
