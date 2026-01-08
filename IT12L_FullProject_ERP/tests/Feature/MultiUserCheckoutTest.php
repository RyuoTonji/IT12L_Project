<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MultiUserCheckoutTest extends TestCase
{
    // use RefreshDatabase; // Use this carefully if you want to wipe DB. If not, manage data manually.
    // For this specific environment, avoid wiping if you want to keep user's data, OR use transactions.
    // Given the request, I'll use transactions if possible, or just cleanup.
    use RefreshDatabase;

    public function test_full_order_flow_with_profile_save_and_admin_approval()
    {
        // 1. Setup Data
        $branch = Branch::create([
            'name' => 'Test Branch',
            'address' => 'Test Address',
            'phone' => '09170000000',
            'is_main' => true
        ]);

        $category = \App\Models\Category::create(['name' => 'Test Category']);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Desc',
            'price' => 100,
            'category_id' => $category->id,
            'branch_id' => $branch->id,
            'stock' => 50,
            'is_available' => true
        ]);

        // 2. Register New User (Empty Profile)
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testflow@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'address' => null,
            'phone' => null,
            'role' => 'customer'
        ]);

        // 3. Login as User
        $this->actingAs($user);

        // 4. Simulate Cart (Session)
        $cart = [
            $product->id => [
                'id' => $product->id,
                'quantity' => 1,
                'price' => $product->price,
                'name' => $product->name,
                'branch_id' => $branch->id
            ]
        ];
        session(['cart' => $cart]);

        // Also set 'checkout_cart' as CheckoutController uses it
        session(['checkout_cart' => $cart]);

        // 5. Post to Checkout Process
        // Simulate filling address/phone and checking save boxes
        $response = $this->post(route('checkout.process'), [
            'customer_name' => 'Test User Updated',
            'customer_phone' => '09999999999',
            'address' => '123 New Address St',
            'payment_method' => 'cash',
            'save_address' => '1',
            'save_phone' => '1',
            'notes' => 'Test notes'
        ]);

        // Assert Redirect to Confirm
        $response->assertRedirect();

        // 6. Verify Profile is Updated
        $user->refresh();
        $this->assertEquals('123 New Address St', $user->address, 'User address should be updated');
        $this->assertEquals('09999999999', $user->phone, 'User phone should be updated');

        // 7. Verify Order Created
        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);

        // 8. Logout User / Login Admin
        // Create Admin
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'is_admin' => true
        ]);

        $this->actingAs($admin);

        // 9. Admin Approves Order
        $response = $this->patch(route('admin.orders.updateStatus', $order->id), [
            'status' => 'confirmed'
        ]);

        $response->assertRedirect(); // Should redirect back

        // 10. Verify Order Approval Data
        $order->refresh();
        $this->assertEquals('confirmed', $order->status);
        $this->assertEquals($admin->id, $order->approved_by);
        $this->assertNotNull($order->approved_at);

        // 11. Login as User to View Timeline
        $this->actingAs($user);

        $response = $this->get(route('orders.show', $order->id));
        $response->assertStatus(200);
        $response->assertSee('Your order has been confirmed');
        $response->assertSee('by');
        $response->assertSee('Super Admin');
    }
}
