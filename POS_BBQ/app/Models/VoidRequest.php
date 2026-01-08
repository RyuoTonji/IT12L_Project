<?php

namespace App\Models;

use App\Traits\SyncsToSupabase;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VoidRequest
 *
 * @property int $id
 * @property int $order_id
 * @property int $requester_id
 * @property int|null $approver_id
 * @property string $reason
 * @property array|null $reason_tags
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\User $requester
 * @property-read \App\Models\User|null $approver
 */
class VoidRequest extends Model
{
    use SyncsToSupabase;

    protected $table = 'pos_void_requests';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);

        static::creating(function ($voidRequest) {
            if (!$voidRequest->branch_id && $voidRequest->order) {
                $voidRequest->branch_id = $voidRequest->order->branch_id;
            }
        });
    }


    protected $fillable = [
        'branch_id',
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
