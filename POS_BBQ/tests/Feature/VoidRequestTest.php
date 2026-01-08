<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\VoidRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoidRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_request_void()
    {
        $branch = \App\Models\Branch::factory()->create();
        $cashier = User::factory()->create(['role' => 'cashier', 'branch_id' => $branch->id]);
        $order = Order::factory()->create(['user_id' => $cashier->id, 'branch_id' => $cashier->branch_id, 'status' => 'new']);

        $response = $this->actingAs($cashier)
            ->post(route('orders.request-void', $order), [
                'reason' => 'Customer changed mind',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pos_void_requests', [
            'order_id' => $order->id,
            'requester_id' => $cashier->id,
            'reason' => 'Customer changed mind',
            'status' => 'pending',
        ]);
    }

    public function test_manager_can_approve_void_request()
    {
        $branch = \App\Models\Branch::factory()->create();
        $manager = User::factory()->create(['role' => 'manager', 'branch_id' => $branch->id]);
        $cashier = User::factory()->create(['role' => 'cashier', 'branch_id' => $branch->id]);
        $order = Order::factory()->create(['user_id' => $cashier->id, 'branch_id' => $branch->id, 'status' => 'new']);

        $voidRequest = VoidRequest::create([
            'order_id' => $order->id,
            'requester_id' => $cashier->id,
            'reason' => 'Mistake',
            'status' => 'pending',
            'branch_id' => $branch->id
        ]);

        $response = $this->actingAs($manager)
            ->post(route('manager.void-requests.approve', $voidRequest));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pos_void_requests', [
            'id' => $voidRequest->id,
            'status' => 'approved',
            'approver_id' => $manager->id,
        ]);

        $this->assertDatabaseHas('pos_orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_manager_can_reject_void_request()
    {
        $branch = \App\Models\Branch::factory()->create();
        $manager = User::factory()->create(['role' => 'manager', 'branch_id' => $branch->id]);
        $cashier = User::factory()->create(['role' => 'cashier', 'branch_id' => $branch->id]);
        $order = Order::factory()->create(['user_id' => $cashier->id, 'branch_id' => $branch->id, 'status' => 'new']);

        $voidRequest = VoidRequest::create([
            'order_id' => $order->id,
            'requester_id' => $cashier->id,
            'reason' => 'Mistake',
            'status' => 'pending',
            'branch_id' => $branch->id
        ]);

        $response = $this->actingAs($manager)
            ->post(route('manager.void-requests.reject', $voidRequest));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pos_void_requests', [
            'id' => $voidRequest->id,
            'status' => 'rejected',
            'approver_id' => $manager->id,
        ]);

        $this->assertDatabaseHas('pos_orders', [
            'id' => $order->id,
            'status' => 'new', // Status should not change
        ]);
    }
}
