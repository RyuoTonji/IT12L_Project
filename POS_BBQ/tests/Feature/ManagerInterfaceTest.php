<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\Order;
use App\Models\VoidRequest;
use App\Models\ShiftReport;
use App\Models\Category;
use App\Models\Table;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerInterfaceTest extends TestCase
{
    use RefreshDatabase;

    protected $manager;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable middleware that might interfere (like authentication redirects if we handle actingAs)
        // actually actingAs handles auth middleware.

        // Create Branch manually
        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TB001',
            'address' => '123 Test St',
            'phone' => '1234567890',
            'is_active' => true,
        ]);

        // Create manager
        $this->manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branch->id
        ]);
    }

    public function test_manager_can_access_dashboard()
    {
        $response = $this->actingAs($this->manager)
            ->get(route('manager.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Overview');
    }

    public function test_manager_can_view_void_requests_with_search()
    {
        // Setup dependencies
        $table = Table::create(['branch_id' => $this->branch->id, 'name' => 'UniqueTableOne', 'capacity' => 4, 'status' => 'available']);

        // Create an order
        $order = Order::create([
            'table_id' => $table->id,
            'user_id' => $this->manager->id,
            'branch_id' => $this->branch->id,
            'customer_name' => 'UniqueSearchableCustomer',
            'order_type' => 'dine-in',
            'status' => 'completed',
            'total_amount' => 100.00,
            'payment_status' => 'paid'
        ]);

        // Create void request
        $voidRequest = VoidRequest::create([
            'order_id' => $order->id,
            'requester_id' => $this->manager->id,
            'reason' => 'Wrong Item',
            'status' => 'pending'
        ]);

        // Test basic list matches
        $response = $this->actingAs($this->manager)
            ->get(route('manager.void-requests.index'));
        $response->assertStatus(200);
        $response->assertSee($order->id); // Order ID usually displayed

        // Test search matches
        $response = $this->actingAs($this->manager)
            ->get(route('manager.void-requests.index', ['search' => 'UniqueSearchableCustomer']));
        $response->assertStatus(200);
        $response->assertSee('UniqueSearchableCustomer');

        // Test search filters out mismatch
        $response = $this->actingAs($this->manager)
            ->get(route('manager.void-requests.index', ['search' => 'NonExistentString']));
        $response->assertStatus(200);
        $response->assertDontSee('UniqueSearchableCustomer');
    }

    public function test_manager_can_approve_void_request()
    {
        $table = Table::create(['branch_id' => $this->branch->id, 'name' => 'Table 2', 'capacity' => 4, 'status' => 'available']);

        $order = Order::create([
            'table_id' => $table->id,
            'user_id' => $this->manager->id,
            'branch_id' => $this->branch->id,
            'customer_name' => 'To Void',
            'order_type' => 'dine-in',
            'status' => 'completed',
            'total_amount' => 50.00,
            'payment_status' => 'paid'
        ]);

        $voidRequest = VoidRequest::create([
            'order_id' => $order->id,
            'requester_id' => $this->manager->id,
            'reason' => 'Test Void',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->manager)
            ->post(route('manager.void-requests.approve', $voidRequest));

        $response->assertRedirect();

        $this->assertDatabaseHas('pos_void_requests', [
            'id' => $voidRequest->id,
            'status' => 'approved',
            'approver_id' => $this->manager->id
        ]);

        // Order should be cancelled
        $this->assertDatabaseHas('pos_orders', [
            'id' => $order->id,
            'status' => 'cancelled'
        ]);
    }

    public function test_manager_can_view_shift_report_details()
    {
        $report = ShiftReport::create([
            'user_id' => $this->manager->id,
            'report_type' => 'sales',
            'shift_date' => now(),
            'total_sales' => 1000.00,
            'total_refunds' => 0.00,
            'total_orders' => 10,
            'status' => 'submitted',
            'branch_id' => $this->branch->id,
            'content' => 'End of shift report content.'
        ]);

        // Note: Using the route name 'admin.shift-reports.show' as we kept it, verify shared access
        $response = $this->actingAs($this->manager)
            ->get(route('admin.shift-reports.show', $report));

        $response->assertStatus(200);
        $response->assertSee('Shift Report Details');
    }

    public function test_manager_daily_report_access()
    {
        $response = $this->actingAs($this->manager)
            ->get(route('manager.reports.daily'));

        $response->assertStatus(200);
        $response->assertSee('Daily Consolidated Report');
    }
}
