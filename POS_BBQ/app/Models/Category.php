<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes;

    protected $fillable = ['name', 'description', 'sort_order'];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
