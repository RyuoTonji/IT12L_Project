<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_clicking_logo_redirects_to_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        // The dashboard route returns the view 'dashboard', which extends 'layouts.app'.
        // 'layouts.app' includes 'layouts.navigation'.
        // We check if the response contains the link to admin dashboard.
        $response->assertSee(route('admin.dashboard'));
        $response->assertDontSee(route('cashier.dashboard'));
    }

    public function test_cashier_clicking_logo_redirects_to_cashier_dashboard()
    {
        $cashier = User::factory()->create(['role' => 'cashier']);

        $response = $this->actingAs($cashier)->get(route('dashboard'));

        $response->assertSee(route('cashier.dashboard'));
        $response->assertDontSee(route('admin.dashboard'));
    }
}
