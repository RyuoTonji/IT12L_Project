<?php

namespace App\Models;

use App\Traits\SyncsToSupabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $order_id
 * @property float $amount
 * @property string $payment_method
 * @property array|null $payment_details
 * @property int $branch_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \App\Models\Branch $branch
 * @property-read \App\Models\Order $order
 */
class Payment extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes, SyncsToSupabase;

    protected $table = 'pos_payments';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);
    }


    protected $fillable = ['order_id', 'amount', 'payment_method', 'payment_details', 'branch_id'];

    protected $casts = [
        'payment_details' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
