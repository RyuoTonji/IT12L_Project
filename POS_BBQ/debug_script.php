<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = \App\Models\Order::find(201);
if ($order) {
    echo "Order Total: " . $order->total_amount . "\n";
    echo "Payment Status: " . $order->payment_status . "\n";
    echo "Payments Sum: " . $order->payments()->sum('amount') . "\n";
    foreach ($order->payments as $p) {
        echo " - Payment: " . $p->amount . "\n";
    }
} else {
    echo "Order 201 not found.\n";
}
