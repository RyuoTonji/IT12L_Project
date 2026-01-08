<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = [
    'pos_activities',
    'pos_shift_reports',
    'pos_inventory_adjustments',
];

foreach ($tables as $table) {
    echo "--- Table: {$table} ---" . PHP_EOL;
    try {
        $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
        print_r($columns);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
    }
    echo PHP_EOL;
}
