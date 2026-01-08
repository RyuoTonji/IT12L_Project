<?php

namespace App\Models;

use App\Traits\SyncsToSupabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int|null $table_id
 * @property int $user_id
 * @property int $branch_id
 * @property string|null $customer_name
 * @property string $order_type
 * @property string $status
 * @property float $total_amount
 * @property string $payment_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \App\Models\Table|null $table
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $orderItems
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VoidRequest[] $voidRequests
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 */
class Order extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes, SyncsToSupabase;

    protected $table = 'pos_orders';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);
    }

    protected $fillable = [
        'table_id',
        'user_id',
        'branch_id',
        'customer_name',
        'order_type',
        'status',
        'total_amount',
        'payment_status'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function voidRequests()
    {
        return $this->hasMany(VoidRequest::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
