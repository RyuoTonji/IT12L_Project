<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedData extends Model
{
    use HasFactory;

    protected $table = 'deleted_data';

    protected $fillable = [
        'table_name',
        'record_id',
        'data',
        'deleted_at',
        'deleted_by',
    ];

    protected $casts = [
        'data' => 'array',
        'deleted_at' => 'datetime',
    ];
}
