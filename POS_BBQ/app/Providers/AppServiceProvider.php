<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Tailwind for pagination
        \Illuminate\Pagination\Paginator::useTailwind();
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.custom');

        // Share pending void requests count with admin and manager layouts
        view()->composer(
            ['layouts.admin', 'layouts.manager'],
            \App\View\Composers\VoidRequestComposer::class
        );
    }
}
