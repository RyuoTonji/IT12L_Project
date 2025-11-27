<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'given_name',
        'surname',
        'middle_initial',
        'suffix',
        'name',
        'email',
        'contact_number',
        'address',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Auto-sync the required `name` field for Laravel Auth
    protected static function booted(): void
    {
        static::saving(function ($user) {
            $fullName = trim("{$user->given_name} {$user->surname}");
            $user->name = $fullName ?: $user->email;
        });
    }

    // Nice accessor for views
    public function getFullNameAttribute(): string
    {
        return trim("{$this->given_name} {$this->surname}");
    }
}