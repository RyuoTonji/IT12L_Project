<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Branch;
use App\Models\Product;

class SampleOrdersSeeder extends Seeder
{
    /**
     * Seed 20 sample pending orders for existing users.
     */
    public function run(): void
    {
        $this->command->info('Seeding 20 sample pending orders...');

        // Get existing users (excluding admin)
        $users = User::where('is_admin', false)->get();
        
        if ($users->isEmpty()) {
            $this->command->error('No non-admin users found! Please seed users first.');
            return;
        }

        // Get existing branches
        $branches = Branch::all();
        
        if ($branches->isEmpty()) {
            $this->command->error('No branches found! Please seed branches first.');
            return;
        }

        // Get available products
        $products = Product::where('is_available', 1)->get();
        
        if ($products->isEmpty()) {
            $this->command->error('No products found! Please seed products first.');
            return;
        }

        // Sample notes for pickup orders
        $notes = [
            'I will pick up at 6 PM',
            'Please prepare by 5:30 PM',
            'Picking up for office',
            'No onions please',
            'Extra sauce on the side',
            'Make it spicy',
            'Less salt please',
            null,
            'Will arrive in 30 minutes',
            'Picking up for family dinner'
        ];

        // Define months in 2025 for random distribution
        $months = [
            ['month' => 1, 'name' => 'January'],
            ['month' => 2, 'name' => 'February'],
            ['month' => 3, 'name' => 'March'],
            ['month' => 4, 'name' => 'April'],
            ['month' => 5, 'name' => 'May'],
            ['month' => 6, 'name' => 'June'],
            ['month' => 7, 'name' => 'July'],
            ['month' => 8, 'name' => 'August'],
            ['month' => 9, 'name' => 'September'],
            ['month' => 10, 'name' => 'October'],
            ['month' => 11, 'name' => 'November'],
            ['month' => 12, 'name' => 'December'],
        ];

        // Create 20 pending orders
        for ($i = 1; $i <= 40; $i++) {
            // Randomly select user, branch
            $user = $users->random();
            $branch = $branches->random();
            
            // Get random products from this branch
            $branchProducts = $products->where('branch_id', $branch->id);
            
            if ($branchProducts->isEmpty()) {
                continue;
            }

            // Randomly select 1-5 products
            $orderProducts = $branchProducts->random(rand(1, min(5, $branchProducts->count())));
            
            $totalAmount = 0;
            $orderItems = [];

            // Calculate total and prepare order items
            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Select random month from 2025
            $randomMonth = $months[array_rand($months)];
            $month = $randomMonth['month'];
            $monthName = $randomMonth['name'];
            
            // Get random day in that month
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, 2025);
            $day = rand(1, $daysInMonth);
            
            // Create date in 2025
            $orderDate = \Carbon\Carbon::create(2025, $month, $day, rand(8, 20), rand(0, 59), 0);

            // Create the order (pickup only - no address)
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'customer_name' => $user->name,
                'customer_phone' => $user->phone ?? '0917' . rand(1000000, 9999999),
                'notes' => $notes[array_rand($notes)],
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);

            // Insert order items with matching timestamps
            foreach ($orderItems as &$item) {
                $item['order_id'] = $orderId;
                $item['created_at'] = $orderDate;
                $item['updated_at'] = $orderDate;
            }
            DB::table('order_items')->insert($orderItems);

            $this->command->info("✓ Order #{$orderId} created for {$user->name} - ₱{$totalAmount} ({$monthName} {$day}, 2025)");
        }

        $this->command->info('');
        $this->command->info('================================================');
        $this->command->info('✓ Successfully seeded 20 pending orders!');
        $this->command->info('================================================');
    }
}