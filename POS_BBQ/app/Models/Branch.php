<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\SyncsToSupabase;

/**
 * App\Models\Branch
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $address
 * @property string|null $phone
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Inventory[] $inventories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShiftReport[] $shiftReports
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MenuItem[] $menuItems
 */
class Branch extends Model
{
    use HasFactory, SoftDeletes, SyncsToSupabase;
    protected $table = 'pos_branches';

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
        return $this->belongsToMany(MenuItem::class, 'pos_menu_item_branch')
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
