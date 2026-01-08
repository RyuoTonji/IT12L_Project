<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$amount = 318;
echo "Searching for order with total: {$amount}" . PHP_EOL;

$order = App\Models\Order::where('total_amount', $amount)->latest()->first();
if ($order) {
    echo "Found Order ID: {$order->id}" . PHP_EOL;
    echo "Customer Name: {$order->customer_name}" . PHP_EOL;
    echo "Created At: {$order->created_at}" . PHP_EOL;

    $sync = Illuminate\Support\Facades\DB::connection('supabase')
        ->table('pos_orders')
        ->where('id', $order->id)
        ->first();
    echo "Sync Status: " . ($sync ? 'SYNCED' : 'NOT SYNCED') . PHP_EOL;
} else {
    echo "Not Found" . PHP_EOL;
    // Let's also check the last 10 orders just in case
    echo "Last 10 orders:" . PHP_EOL;
    $lastOrders = App\Models\Order::latest('id')->take(10)->get();
    foreach ($lastOrders as $lo) {
        echo "ID: {$lo->id} | Total: {$lo->total_amount} | Created: {$lo->created_at}" . PHP_EOL;
    }
}
