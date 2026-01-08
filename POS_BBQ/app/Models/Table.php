<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;
use App\Traits\SyncsToSupabase;

/**
 * App\Models\Table
 *
 * @property int $id
 * @property string $name
 * @property int $capacity
 * @property string $status
 * @property int $branch_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \App\Models\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 */
class Table extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes, SyncsToSupabase;

    protected $table = 'pos_tables';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);
    }

    protected $fillable = ['name', 'capacity', 'status', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
