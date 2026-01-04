<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use SoftDeletes;
    
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'order_id',
        'customer_name',
        'customer_email',
        'feedback_type',
        'customer_type',
        'message',
        'status',
    ];

    // Status constants
    const STATUS_NEW = 'new';
    const STATUS_READ = 'read';
    const STATUS_RESOLVED = 'resolved';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('feedback_type', $type);
    }
}
