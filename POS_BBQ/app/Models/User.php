<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, LogsDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_id',
        'status',
        'hourly_rate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isInventory()
    {
        return $this->role === 'inventory';
    }

    /**
     * Check if user is currently active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is disabled (terminated, cannot login)
     */
    public function isDisabled()
    {
        return $this->status === 'disabled';
    }

    /**
     * Get computed status based on last login
     * Returns 'inactive' if user hasn't logged in for X days, otherwise returns actual status
     */
    public function getComputedStatus($inactiveDays = 3)
    {
        // Disabled always stays disabled
        if ($this->status === 'disabled') {
            return 'disabled';
        }

        // Check last login time
        if ($this->last_login_at) {
            $daysSinceLogin = now()->diffInDays($this->last_login_at);
            if ($daysSinceLogin >= $inactiveDays) {
                return 'inactive';
            }
        } elseif ($this->created_at) {
            // If never logged in, check days since creation
            $daysSinceCreation = now()->diffInDays($this->created_at);
            if ($daysSinceCreation >= $inactiveDays) {
                return 'inactive';
            }
        }

        return $this->status;
    }

    /**
     * Get status display label
     */
    public function getStatusLabel()
    {
        $computedStatus = $this->getComputedStatus();

        return match ($computedStatus) {
            'active' => 'Active',
            'inactive' => $this->last_login_at
            ? 'Inactive (' . now()->diffInDays($this->last_login_at) . ' days)'
            : 'Inactive (Never logged in)',
            'disabled' => 'Disabled',
            default => ucfirst($computedStatus),
        };
    }
}
