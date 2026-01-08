<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutConfirmTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_confirm_page_displays_branch_address()
    {
        // 1. Setup Data
        $branch = Branch::create([
            'name' => 'Test Branch',
            'address' => '123 Test St, Test City',
            'phone' => '09171234567',
            'is_main' => true
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'testconfirm@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'customer'
        ]);

        $this->actingAs($user);

        // 2. Create Order
        $order = Order::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'total_amount' => 500,
            'status' => 'pending',
            'address' => 'User Address',
            'customer_name' => 'User Name',
            'customer_phone' => '09170000000',
        ]);

        // 3. Visit Confirm Page
        $response = $this->get(route('checkout.confirm', ['order_id' => $order->id]));

        // 4. Assertions
        $response->assertStatus(200);
        $response->assertViewIs('user.checkout.confirm');
        $response->assertSee('Test Branch'); // Branch Name
        $response->assertSee('123 Test St, Test City'); // Branch Address
        $response->assertSee('09171234567'); // Branch Phone (if displayed)
    }

    public function test_checkout_process_returns_json_for_ajax_request()
    {
        // 1. Setup Data
        $branch = Branch::create([
            'name' => 'Test Branch',
            'is_main' => true
        ]);

        $product = \App\Models\Product::create([
            'name' => 'Test Product',
            'price' => 100,
            'branch_id' => $branch->id,
            'category_id' => \App\Models\Category::create(['name' => 'Cat'])->id
        ]);

        $user = User::create([
            'name' => 'Json User',
            'email' => 'json@test.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $this->actingAs($user);

        // 2. Setup Cart Session
        session([
            'checkout_cart' => [
                $product->id => [
                    'id' => $product->id,
                    'quantity' => 1,
                    'price' => 100,
                    'branch_id' => $branch->id
                ]
            ]
        ]);

        // 3. Make AJAX Request
        $response = $this->postJson(route('checkout.process'), [
            'customer_name' => 'Json User',
            'customer_phone' => '09000000000',
            'address' => 'Json Address',
            'payment_method' => 'cash'
        ]);

        // 4. Assert JSON Response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'payment_method' => 'cash'
            ])
            ->assertJsonStructure(['redirect_url', 'order_id']);
    }
}
