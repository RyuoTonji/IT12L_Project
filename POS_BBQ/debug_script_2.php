<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Order 201...\n";
$order = \App\Models\Order::find(201);
if ($order) {
    echo "Order Total: " . $order->total_amount . "\n";
    echo "Payment Status: " . $order->payment_status . "\n";

    $payments = \App\Models\Payment::where('order_id', 201)->get();
    echo "Payments Count (Direct Query): " . $payments->count() . "\n";
    echo "Payments Sum: " . $payments->sum('amount') . "\n";

    foreach ($payments as $p) {
        echo " - Payment ID: " . $p->id . ", Amount: " . $p->amount . ", Branch: " . $p->branch_id . "\n";
    }
} else {
    echo "Order 201 not found.\n";
}
