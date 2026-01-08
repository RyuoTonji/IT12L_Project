<?php

use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Branch;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== POS SYNC VERIFICATION ===" . PHP_EOL . PHP_EOL;

// 1. Connection Test
echo "1. Connection Check:" . PHP_EOL;
try {
    $localCount = DB::connection('mysql')->table('pos_orders')->count();
    echo "✓ Local MySQL (pos_orders): {$localCount}" . PHP_EOL;

    $supabaseCount = DB::connection('supabase')->table('pos_orders')->count();
    echo "✓ Supabase (pos_orders): {$supabaseCount}" . PHP_EOL;
} catch (Exception $e) {
    echo "✗ Connection Error: " . $e->getMessage() . PHP_EOL;
}
echo PHP_EOL;

// 2. Data Integrity
echo "2. Model Audit:" . PHP_EOL;
$order = Order::first();
if ($order) {
    echo "✓ Sample Order ID: {$order->id}" . PHP_EOL;
    echo "✓ Order Branch ID: {$order->branch_id}" . PHP_EOL;
    $branch = Branch::find($order->branch_id);
    echo "✓ Branch Name: " . ($branch ? $branch->name : 'NOT FOUND') . PHP_EOL;
} else {
    echo "⚠ No orders found in local database." . PHP_EOL;
}
echo PHP_EOL;

// 3. Sync Logic Verification
echo "3. Sync Status Check:" . PHP_EOL;
$unsynced = Order::where(function ($query) {
    // Check if there's any mechanism to track sync status on the model itself if added later
    // For now, we compare counts or look at logs.
})->count();

echo "✓ Total orders in local: " . Order::count() . PHP_EOL;
try {
    echo "✓ Total orders in Supabase: " . DB::connection('supabase')->table('pos_orders')->count() . PHP_EOL;
} catch (Exception $e) {
    echo "✗ Could not fetch Supabase count" . PHP_EOL;
}

echo PHP_EOL . "=== VERIFICATION COMPLETE ===" . PHP_EOL;
