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
}