<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    $count = \Illuminate\Support\Facades\DB::table('jobs')->count();
    echo "Pending jobs count: " . $count . PHP_EOL;

    if ($count > 0) {
        $jobs = \Illuminate\Support\Facades\DB::table('jobs')->get();
        foreach ($jobs as $job) {
            echo "Job ID: " . $job->id . " - Payload: " . substr($job->payload, 0, 100) . "..." . PHP_EOL;
        }
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
