<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes, LogsDeletes;

    protected $fillable = ['name', 'capacity', 'status'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
