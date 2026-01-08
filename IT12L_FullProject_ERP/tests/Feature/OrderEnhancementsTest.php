<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class OrderEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_updates_populate_timestamps_and_admin_view_images()
    {
        // 1. Setup Data
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
            'is_admin' => true
        ]);

        $customer = User::create([
            'name' => 'Customer',
            'email' => 'cust@test.com',
            'password' => 'password'
        ]);

        $branch = Branch::create(['name' => 'Branch 1', 'is_main' => true, 'address' => 'Branch Addr', 'phone' => '123']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 100,
            'branch_id' => $branch->id,
            'category_id' => \App\Models\Category::create(['name' => 'Cat'])->id,
            'image' => 'products/test.png' // Simulate image path
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'branch_id' => $branch->id,
            'total_amount' => 100,
            'status' => 'pending',
            'customer_name' => 'Cust Name',
            'customer_phone' => '09123',
            'address' => 'Customer Address 123'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100
        ]);

        $this->actingAs($admin);

        // 2. Test Timestamps Logic via Controller updateStatus

        // Status -> Preparing
        $this->patch(route('admin.orders.updateStatus', $order->id), ['status' => 'preparing']);
        $order->refresh();
        $this->assertNotNull($order->preparing_at);
        $this->assertEquals('preparing', $order->status);

        // Status -> Ready
        $this->patch(route('admin.orders.updateStatus', $order->id), ['status' => 'ready']);
        $order->refresh();
        $this->assertNotNull($order->ready_at);
        $this->assertEquals('ready', $order->status);

        // Status -> Picked Up
        $this->patch(route('admin.orders.updateStatus', $order->id), ['status' => 'picked up']);
        $order->refresh();
        $this->assertNotNull($order->picked_up_at);
        $this->assertEquals('picked up', $order->status);

        // 3. Test User View (Order Details)
        $response = $this->actingAs($customer)->get(route('orders.show', $order->id));
        $response->assertStatus(200);

        // Check for specific timestamps display format (just checking partials)
        $response->assertSee('Started at ' . $order->preparing_at->format('g:i A'));
        $response->assertSee('Ready at ' . $order->ready_at->format('g:i A'));
        $response->assertSee('Picked up at ' . $order->picked_up_at->format('g:i A'));

        // Check Address
        $response->assertSee('Customer Address 123');

        // Check Thank You Note
        $response->assertSee('Thank You!');

        // 4. Test Admin View Images (Regression check)
        $responseAdmin = $this->actingAs($admin)->get(route('admin.orders.show', $order->id));
        $responseAdmin->assertStatus(200);
        // The view logic now checks file_exists. Since the file doesn't actually exist in this test environment, 
        // it should hit the placeholder.
        // To test the positive case, we'd need to mock file_exists or create a dummy file. 
        // For now, verifying the request succeeds is good. 
        // Let's assert we DON'T see the broken image src if file is missing.
        $responseAdmin->assertDontSee('storage/products/test.png');
        $responseAdmin->assertSee('fa-utensils'); // Placeholder
    }
}
