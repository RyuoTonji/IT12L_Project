<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    echo "Attempting to find Order #205 in Supabase..." . PHP_EOL;

    $order = \Illuminate\Support\Facades\DB::connection('supabase')
        ->table('orders')
        ->where('id', 205)
        ->first();

    if ($order) {
        echo "FOUND Order #205 in Supabase!" . PHP_EOL;
        echo "Customer: " . $order->customer_name . PHP_EOL;
        echo "Total: " . $order->total_amount . PHP_EOL;
    } else {
        echo "Order #205 NOT FOUND in Supabase." . PHP_EOL;

        // List recent orders from Supabase to see if connection works at all
        echo "Listing recent orders from Supabase:" . PHP_EOL;
        $recent = \Illuminate\Support\Facades\DB::connection('supabase')
            ->table('orders')
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get();

        foreach ($recent as $o) {
            echo "ID: " . $o->id . " - " . $o->customer_name . PHP_EOL;
        }
    }

} catch (\Exception $e) {
    echo "Error connecting/reading from Supabase: " . $e->getMessage() . PHP_EOL;
}
