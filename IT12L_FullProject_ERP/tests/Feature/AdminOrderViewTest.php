<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_order_show_page_displays_product_with_image_relation()
    {
        // 1. Setup Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
            'is_admin' => true
        ]);

        $branch = Branch::create(['name' => 'Branch 1', 'is_main' => true]);

        // 2. Create Product with Image
        $product = Product::create([
            'name' => 'Image Product',
            'price' => 100,
            'branch_id' => $branch->id,
            'category_id' => \App\Models\Category::create(['name' => 'Cat'])->id,
            'image' => 'products/test-image.png'
        ]);

        // 3. Create Order
        $order = Order::create([
            'user_id' => $admin->id,
            'branch_id' => $branch->id,
            'total_amount' => 100,
            'status' => 'pending',
            'customer_name' => 'Cust',
            'customer_phone' => '000'
        ]);

        // 4. Create OrderItem (WITHOUT local product_image to test relation fallback)
        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100
        ]);

        $this->actingAs($admin);

        // 5. Visit Admin Order Show
        $response = $this->get(route('admin.orders.show', $order->id));

        // 6. Assertions
        $response->assertStatus(200);
        $response->assertSee('Image Product');
        // Check for the image path constructed via relationship
        $response->assertSee('storage/products/test-image.png');
    }
}
