<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
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
            \Illuminate\Support\Facades\DB::table('deletion_logs')->insert([
                'table_name' => 'categories',
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