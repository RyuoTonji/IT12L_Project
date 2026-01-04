<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

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
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        static::deleted(function ($order) {
            \Illuminate\Support\Facades\DB::table('deletion_logs')->insert([
                'table_name' => 'orders',
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