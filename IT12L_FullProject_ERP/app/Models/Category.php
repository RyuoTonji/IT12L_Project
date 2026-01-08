<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 */
class Category extends Model
{
    use HasFactory;
    protected $table = 'crm_categories';
    use SoftDeletes; // ← THIS LINE

    protected $fillable = [
        'name',
        'description',
    ];

    public $timestamps = true; // recommended

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted()
    {
        static::deleted(function ($category) {
            \Illuminate\Support\Facades\DB::table('crm_deletion_logs')->insert([
                'table_name' => 'crm_categories',
                'record_id' => $category->id,
                'data' => json_encode($category->toArray()),
                'deleted_by' => auth()->id(),
                'reason' => 'Soft delete',
                'deleted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}