<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes;

    protected $fillable = ['order_id', 'amount', 'payment_method', 'payment_details'];

    protected $casts = [
        'payment_details' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
