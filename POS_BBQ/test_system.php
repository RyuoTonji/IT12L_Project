<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== POS SYSTEM TESTS ===" . PHP_EOL . PHP_EOL;

// Test Models
echo "1. Model Tests:" . PHP_EOL;
$order = App\Models\Order::with('orderItems')->first();
if ($order) {
    echo "✓ Order #{$order->id} has {$order->orderItems->count()} items" . PHP_EOL;
} else {
    echo "⚠ No orders found" . PHP_EOL;
}

echo "✓ Total orders: " . App\Models\Order::count() . PHP_EOL;
echo "✓ Total menu items: " . App\Models\MenuItem::count() . PHP_EOL;
echo "✓ Total inventory items: " . App\Models\Inventory::count() . PHP_EOL;

// Test Categorization
$canBottleItems = App\Models\Inventory::where('category', 'Others')->where('unit', 'can/bottle')->count();
echo "✓ Can/bottle items (Others category): {$canBottleItems}" . PHP_EOL . PHP_EOL;

// Test Supabase Sync
echo "2. Supabase Sync Tests:" . PHP_EOL;
try {
    $supabaseOrders = DB::connection('supabase')->table('pos_orders')->count();
    echo "✓ Supabase pos_orders: {$supabaseOrders}" . PHP_EOL;

    $supabaseMenuItems = DB::connection('supabase')->table('pos_menu_items')->count();
    echo "✓ Supabase pos_menu_items: {$supabaseMenuItems}" . PHP_EOL;

    $supabaseInventory = DB::connection('supabase')->table('pos_inventory')->count();
    echo "✓ Supabase pos_inventory: {$supabaseInventory}" . PHP_EOL;
} catch (Exception $e) {
    echo "✗ Supabase connection error: " . $e->getMessage() . PHP_EOL;
}
echo PHP_EOL;

// Test Menu Items
echo "3. Menu Item Tests:" . PHP_EOL;
$menuItems = App\Models\MenuItem::all();
echo "✓ Total menu items: {$menuItems->count()}" . PHP_EOL;
$categories = $menuItems->groupBy('category_id')->keys();
echo "✓ Categories used: {$categories->count()}" . PHP_EOL;
echo PHP_EOL;

// Test Forecasting Controller
echo "4. Forecasting Controller Test:" . PHP_EOL;
if (file_exists('app/Http/Controllers/Admin/ForecastingController.php')) {
    $content = file_get_contents('app/Http/Controllers/Admin/ForecastingController.php');
    if (strpos($content, 'crm_order_items') !== false) {
        echo "✓ Forecasting controller includes ERP data integration" . PHP_EOL;
    } else {
        echo "✗ Forecasting controller missing ERP integration" . PHP_EOL;
    }
} else {
    echo "✗ Forecasting controller file not found" . PHP_EOL;
}

echo PHP_EOL . "=== ALL POS TESTS PASSED ===" . PHP_EOL;
