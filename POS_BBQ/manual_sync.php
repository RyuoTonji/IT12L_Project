<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    $order = \App\Models\Order::find(205);
    if (!$order) {
        throw new Exception("Order #205 not found");
    }

    echo "Dispatching sync for Order #205..." . PHP_EOL;

    // Dispatch synchronously to see errors immediately
    \App\Jobs\SyncModelToSupabase::dispatchSync(
        get_class($order),
        $order->getAttributes(),
        $order->getKey(),
        'save'
    );

    echo "Sync dispatched successfully." . PHP_EOL;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}
