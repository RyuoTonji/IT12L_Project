<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RECENT ORDERS LOG ===" . PHP_EOL;
$orders = App\Models\Order::latest('id')->take(5)->get();
foreach ($orders as $o) {
    echo "ID: {$o->id} | Total: {$o->total_amount} | Created: {$o->created_at}" . PHP_EOL;
    $sync = Illuminate\Support\Facades\DB::connection('supabase')->table('pos_orders')->where('id', $o->id)->first();
    echo "  Sync Status: " . ($sync ? 'SYNCED' : 'NOT SYNCED') . PHP_EOL;
}
