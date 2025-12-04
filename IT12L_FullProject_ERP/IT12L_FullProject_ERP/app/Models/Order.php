<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'branch_id',
        'total_amount',
        'status',
        'address',
        'customer_name',
        'customer_phone',
        'notes',
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

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
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
}