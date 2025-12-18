<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    // 1. Check if Order #205 exists
    $order = \App\Models\Order::find(205);
    if ($order) {
        echo "Order #205 FOUND in local DB." . PHP_EOL;
        echo "Status: " . $order->status . PHP_EOL;
        echo "Payment Status: " . $order->payment_status . PHP_EOL;
    } else {
        echo "Order #205 NOT FOUND in local DB." . PHP_EOL;
    }

    // 2. Check failed_jobs
    $failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')->orderBy('id', 'desc')->limit(5)->get();
    echo PHP_EOL . "Recent Failed Jobs: " . $failedJobs->count() . PHP_EOL;
    foreach ($failedJobs as $job) {
        echo "ID: " . $job->id . " | Failed At: " . $job->failed_at . PHP_EOL;
        echo "Exception: " . substr($job->exception, 0, 200) . "..." . PHP_EOL;
    }

    // 3. Check pending jobs count
    $pendingCount = \Illuminate\Support\Facades\DB::table('jobs')->count();
    echo PHP_EOL . "Pending Jobs Count: " . $pendingCount . PHP_EOL;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
