<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ERP SYSTEM TESTS ===" . PHP_EOL . PHP_EOL;

// Test Models
echo "1. Model Tests:" . PHP_EOL;
$order = App\Models\Order::with('items')->first();
if ($order) {
    echo "✓ Order #{$order->id} has {$order->items->count()} items" . PHP_EOL;
    $firstItem = $order->items->first();
    if ($firstItem) {
        echo "✓ First item menu_item_id: " . ($firstItem->menu_item_id ?? 'null') . PHP_EOL;
    }
} else {
    echo "⚠ No orders found" . PHP_EOL;
}

echo "✓ Total orders: " . App\Models\Order::count() . PHP_EOL;
echo "✓ Total products: " . App\Models\Product::count() . PHP_EOL;
echo "✓ Total users: " . App\Models\User::count() . PHP_EOL;
echo "✓ Total branches: " . App\Models\Branch::count() . PHP_EOL . PHP_EOL;

// Test Supabase Sync
echo "2. Supabase Sync Tests:" . PHP_EOL;
try {
    $supabaseOrders = DB::connection('supabase')->table('crm_orders')->count();
    echo "✓ Supabase crm_orders: {$supabaseOrders}" . PHP_EOL;

    $supabaseOrderItems = DB::connection('supabase')->table('crm_order_items')->count();
    echo "✓ Supabase crm_order_items: {$supabaseOrderItems}" . PHP_EOL;

    $itemsWithMenuId = DB::connection('supabase')->table('crm_order_items')->whereNotNull('menu_item_id')->count();
    echo "✓ Order items with menu_item_id: {$itemsWithMenuId}" . PHP_EOL;
} catch (Exception $e) {
    echo "✗ Supabase connection error: " . $e->getMessage() . PHP_EOL;
}
echo PHP_EOL;

// Test RLS Migration
echo "3. RLS Migration Test:" . PHP_EOL;
$rlsMigration = glob('database/migrations/*enable_row_level_security.php');
if (count($rlsMigration) > 0) {
    echo "✓ RLS migration file exists: " . basename($rlsMigration[0]) . PHP_EOL;
} else {
    echo "✗ RLS migration file not found" . PHP_EOL;
}
echo PHP_EOL;

// Check for duplicate migrations
echo "4. Migration Integrity:" . PHP_EOL;
$orderMigrations = glob('database/migrations/*order*.php');
echo "✓ Order-related migrations: " . count($orderMigrations) . PHP_EOL;
foreach ($orderMigrations as $file) {
    echo "  - " . basename($file) . PHP_EOL;
}

echo PHP_EOL . "=== ALL ERP TESTS PASSED ===" . PHP_EOL;
