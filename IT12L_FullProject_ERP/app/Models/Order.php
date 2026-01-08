<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $user_id
 * @property int $branch_id
 * @property float $total_amount
 * @property string $status
 * @property string|null $address
 * @property string|null $customer_name
 * @property string|null $customer_phone
 * @property string|null $notes
 * @property string|null $payment_method
 * @property string|null $paymongo_source_id
 * @property string|null $payment_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $items
 * @property-read \Illuminate\Support\Carbon $ordered_at
 */
class Order extends Model
{
    use SoftDeletes;

    protected $table = 'crm_orders';

    protected $fillable = [
        'user_id',
        'branch_id',
        'total_amount',
        'status',
        'address',
        'customer_name',
        'customer_phone',
        'notes',
        'payment_method',
        'paymongo_source_id',
        'payment_status',
        'approved_by',
        'approved_at',
        'preparing_at',
        'ready_at',
        'picked_up_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approved_at' => 'datetime',
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'picked_up_at' => 'datetime',
    ];

    // Add accessor for ordered_at to use created_at
    public function getOrderedAtAttribute()
    {
        return $this->created_at;
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePickedUp($query)
    {
        return $query->where('status', 'pickedup');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Helper methods
    public function canBeCancelled()
    {
        return $this->status === 'pending';
    }

    public function cancel()
    {
        if ($this->canBeCancelled()) {
            $this->update(['status' => 'cancelled']);
            return true;
        }
        return false;
    }

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\OwnerScope);

        static::deleted(function ($order) {
            \Illuminate\Support\Facades\DB::table('crm_deletion_logs')->insert([
                'table_name' => 'crm_orders',
                'record_id' => $order->id,
                'data' => json_encode($order->toArray()),
                'deleted_by' => auth()->id(),
                'reason' => 'Soft delete',
                'deleted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}