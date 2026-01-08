<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Branch
 *
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property string|null $phone
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 */
class Branch extends Model
{
    use SoftDeletes;
    protected $table = 'crm_branches';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'is_active',
    ];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    protected static function booted()
    {
        static::deleted(function ($branch) {
            \Illuminate\Support\Facades\DB::table('crm_deletion_logs')->insert([
                'table_name' => 'crm_branches',
                'record_id' => $branch->id,
                'data' => json_encode($branch->toArray()),
                'deleted_by' => auth()->id(),
                'reason' => 'Soft delete',
                'deleted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}