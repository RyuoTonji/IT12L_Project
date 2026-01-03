<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Jobs\SyncOrderToSupabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Sync Verification...\n";

// 1. Check if Supabase connection is configured
try {
    $config = config('database.connections.supabase');
    echo "Supabase Host: " . ($config['host'] ?? 'Not set') . "\n";
} catch (\Exception $e) {
    echo "Error reading supabase config: " . $e->getMessage() . "\n";
}

// 2. Create a test order locally
DB::beginTransaction();
try {
    $order = Order::create([
        'user_id' => 1, // Assumes user 1 exists
        'branch_id' => 1, // Assumes branch 1 exists
        'total_amount' => 100.00,
        'status' => 'pending',
        'customer_name' => 'Sync Test',
        'customer_phone' => '09123456789',
    ]);

    $item = OrderItem::create([
        'order_id' => $order->id,
        'product_name' => 'Test Product',
        'quantity' => 1,
        'price' => 100.00,
    ]);

    echo "Order created locally with ID: {$order->id}\n";
    
    // Check if is_synced is false initially
    if (!$order->is_synced) {
        echo "Check: is_synced is false (Correct)\n";
    }

    // Check jobs table
    $jobCount = DB::table('jobs')->count();
    echo "Jobs in queue: {$jobCount}\n";

} catch (\Exception $e) {
    echo "Error creating test order: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "Test order rolled back from local DB.\n";
}

echo "Verification Script Finished.\n";
