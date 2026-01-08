<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = [
    'pos_branches',
    'pos_categories',
    'pos_menu_items',
    'pos_tables',
    'pos_orders',
    'pos_order_items',
    'pos_payments',
    'pos_inventory',
    'pos_void_requests',
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
