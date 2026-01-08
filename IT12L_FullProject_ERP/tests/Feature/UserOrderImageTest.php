<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserOrderImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_order_show_page_displays_images()
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => 'password'
        ]);

        $branch = Branch::create(['name' => 'Branch 1', 'is_main' => true]);
        $product = Product::create([
            'name' => 'Image Product',
            'price' => 100,
            'branch_id' => $branch->id,
            'category_id' => \App\Models\Category::create(['name' => 'Cat'])->id,
            'image' => 'products/test-image.png'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'total_amount' => 100,
            'status' => 'pending',
            'customer_name' => 'Cust',
            'customer_phone' => '000'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100
        ]);

        $this->actingAs($user);

        $response = $this->get(route('orders.show', $order->id));

        $response->assertStatus(200);
        $response->assertSee('Image Product');
        // Check for placeholder since file doesn't exist, but checking structure
        $response->assertSee('fa-utensils');
    }
}
