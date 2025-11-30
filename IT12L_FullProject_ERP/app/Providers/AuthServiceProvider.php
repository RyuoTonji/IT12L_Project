<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define admin gate if you want to check admin access
        Gate::define('admin-access', function ($user) {
            // Add your admin logic here
            // For now, you can check by email or add is_admin field
            return in_array($user->email, [
                'admin@foodorder.com',
            ]);
        });
    }
}