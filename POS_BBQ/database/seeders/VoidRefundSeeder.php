<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Payment;
use App\Models\VoidRequest;
use App\Models\Activity;
use Carbon\Carbon;

class VoidRefundSeeder extends Seeder
{
    public function run()
    {
        $cashier = User::where('role', 'cashier')->first() ?? User::first();
        $manager = User::where('role', 'manager')->first() ?? User::first();
        $menuItems = MenuItem::all();
        $tables = Table::all();

        if ($menuItems->isEmpty() || $tables->isEmpty()) {
            $this->command->error('Missing menu items or tables. Please seed them first.');
            return;
        }

        // Seed 25 Refund Requests (Paid Orders)
        $this->seedRequests(25, 'paid', $cashier, $manager, $menuItems, $tables);

        // Seed 25 Void Requests (Unpaid Orders)
        $this->seedRequests(25, 'pending', $cashier, $manager, $menuItems, $tables);
    }

    private function seedRequests($count, $paymentStatus, $requester, $approver, $menuItems, $tables)
    {
        $type = $paymentStatus === 'paid' ? 'Refund' : 'Void';
        $this->command->info("Seeding {$count} {$type} requests...");

        for ($i = 0; $i < $count; $i++) {
            $table = $tables->random();
            $status = $this->getRandomStatus();

            // Create Order
            $order = Order::create([
                'table_id' => $table->id,
                'user_id' => $requester->id,
                'branch_id' => $requester->branch_id ?? 1,
                'customer_name' => "{$type} Customer " . ($i + 1),
                'order_type' => 'dine-in',
                'status' => 'served', // Initial status
                'payment_status' => $paymentStatus,
                'total_amount' => 0,
                'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 12)),
            ]);

            // Add Items
            $totalAmount = 0;
            $numItems = rand(1, 4);
            for ($j = 0; $j < $numItems; $j++) {
                $item = $menuItems->random();
                $qty = rand(1, 2);
                $price = $item->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                ]);
                $totalAmount += $price * $qty;
            }
            $order->update(['total_amount' => $totalAmount]);

            // If Paid, create Payment
            if ($paymentStatus === 'paid') {
                Payment::create([
                    'order_id' => $order->id,
                    'branch_id' => $order->branch_id,
                    'amount' => $totalAmount,
                    'payment_method' => 'cash',
                    'created_at' => $order->created_at->addMinutes(10),
                ]);
                $order->update(['status' => 'completed']);
            }

            // Create Void/Refund Request
            $voidRequest = VoidRequest::create([
                'order_id' => $order->id,
                'requester_id' => $requester->id,
                'approver_id' => $status !== 'pending' ? $approver->id : null,
                'reason' => "Random {$type} Reason " . rand(1, 100),
                'reason_tags' => ['wrong_item', 'customer_changed_mind'],
                'status' => $status,
                'created_at' => $order->created_at->addMinutes(15),
                'updated_at' => $order->created_at->addMinutes(rand(20, 60)),
            ]);

            // If Approved, update Order status
            if ($status === 'approved') {
                $order->status = 'cancelled';
                if ($paymentStatus === 'paid') {
                    $order->payment_status = 'refunded';
                }
                $order->save();

                // Log Activity (mimicking controller logic)
                Activity::create([
                    'user_id' => $approver->id,
                    'action' => 'approve_void',
                    'details' => "Approved void request for Order #{$order->id}. Reason: {$voidRequest->reason}",
                    'status' => 'warning',
                    'related_id' => $order->id,
                    'related_model' => Order::class,
                    'created_at' => $voidRequest->updated_at,
                ]);
            } elseif ($status === 'rejected') {
                // Log Activity for rejection if needed, though controller only logs approval usually in snippets seen
            }
        }
        $this->command->info("Seeded {$count} {$type} requests.");
    }

    private function getRandomStatus()
    {
        $statuses = ['pending', 'approved', 'rejected'];
        // Weighted random: 20% pending, 40% approved, 40% rejected
        $rand = rand(1, 100);
        if ($rand <= 20)
            return 'pending';
        if ($rand <= 60)
            return 'approved';
        return 'rejected';
    }
}
