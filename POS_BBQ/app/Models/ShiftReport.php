<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_type',
        'shift_date',
        'total_sales',
        'total_refunds',
        'total_orders',
        'stock_in',
        'stock_out',
        'remaining_stock',
        'spoilage',
        'returns',
        'return_reason',
        'content',
        'admin_reply',
        'status',
        'branch_id',
    ];

    protected $casts = [
        'shift_date' => 'date',
        'total_sales' => 'decimal:2',
        'total_refunds' => 'decimal:2',
        'stock_in' => 'decimal:2',
        'stock_out' => 'decimal:2',
        'remaining_stock' => 'decimal:2',
        'spoilage' => 'decimal:2',
        'returns' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
