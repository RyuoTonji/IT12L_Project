<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orderId = $argv[1] ?? 166;

echo "Checking Order ID: {$orderId}" . PHP_EOL;

$order = App\Models\Order::find($orderId);
if ($order) {
    echo "Local: Found in pos_orders" . PHP_EOL;
    echo "Total Amount: {$order->total_amount}" . PHP_EOL;

    try {
        $sync = Illuminate\Support\Facades\DB::connection('supabase')
            ->table('pos_orders')
            ->where('id', $orderId)
            ->first();
        if ($sync) {
            echo "Supabase: Synced to pos_orders" . PHP_EOL;
        } else {
            echo "Supabase: NOT SYNCED" . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "Supabase Error: " . $e->getMessage() . PHP_EOL;
    }
} else {
    echo "Local: NOT FOUND" . PHP_EOL;
}
