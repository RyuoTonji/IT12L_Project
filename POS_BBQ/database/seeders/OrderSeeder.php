<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Payment;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $cashier1 = User::where('email', 'cashier1@example.com')->first();
        $cashier2 = User::where('email', 'cashier2@example.com')->first();
        $menuItems = MenuItem::all();

        if ($menuItems->isEmpty()) {
            $this->command->error('No menu items found. Please seed menu items first.');
            return;
        }

        // Seed Branch 1 Orders
        if ($cashier1) {
            $this->seedBranchOrders($cashier1, 1, 50, 'paid');
        }

        // Seed Branch 2 Orders
        if ($cashier2) {
            $this->seedBranchOrders($cashier2, 2, 50, 'paid');
        }
    }

    private function seedBranchOrders($user, $branchId, $count, $paymentStatus)
    {
        $tables = Table::where('branch_id', $branchId)->get();
        $menuItems = MenuItem::all();

        if ($tables->isEmpty()) {
            $this->command->warn("No tables found for Branch {$branchId}. Skipping.");
            return;
        }

        if (Order::where('branch_id', $branchId)->exists()) {
            $this->command->info("Branch {$branchId} already has orders. Skipping seeding.");
            return;
        }

        for ($i = 0; $i < $count; $i++) {
            $table = $tables->random();

            $order = Order::create([
                'table_id' => $table->id,
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'customer_name' => 'Customer ' . ($i + 1) . ' - ' . ucfirst($paymentStatus),
                'order_type' => 'dine-in',
                'status' => $paymentStatus === 'paid' ? 'completed' : 'served',
                'payment_status' => $paymentStatus,
                'total_amount' => 0, // Will be updated
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 12)),
            ]);

            $totalAmount = 0;
            $itemCount = rand(1, 5);

            for ($j = 0; $j < $itemCount; $j++) {
                $menuItem = $menuItems->random();
                $quantity = rand(1, 3);
                $price = $menuItem->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'notes' => null,
                ]);

                $totalAmount += $price * $quantity;
            }

            $order->update(['total_amount' => $totalAmount]);

            if ($paymentStatus === 'paid') {
                Payment::create([
                    'order_id' => $order->id,
                    'branch_id' => $branchId,
                    'amount' => $totalAmount,
                    'payment_method' => 'cash',
                    'created_at' => $order->created_at->addMinutes(rand(30, 60)),
                ]);
            }
        }

        $this->command->info("Seeded {$count} {$paymentStatus} orders for Branch {$branchId}.");
    }
}
