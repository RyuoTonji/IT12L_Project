<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoidRequest extends Model
{
    protected $fillable = [
        'order_id',
        'requester_id',
        'approver_id',
        'reason',
        'reason_tags',
        'status',
    ];

    protected $casts = [
        'reason_tags' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
