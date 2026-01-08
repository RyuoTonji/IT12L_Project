<?php

namespace App\Models;

use App\Traits\SyncsToSupabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory, SyncsToSupabase;

    protected $table = 'pos_activities';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);

        static::creating(function ($activity) {
            if (!$activity->branch_id && \Illuminate\Support\Facades\Auth::check()) {
                $activity->branch_id = \Illuminate\Support\Facades\Auth::user()->branch_id;
            }
        });
    }


    protected $fillable = [
        'branch_id',
        'user_id',
        'action',
        'details',
        'status',
        'related_id',
        'related_model',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
