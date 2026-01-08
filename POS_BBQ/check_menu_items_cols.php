<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$table = 'pos_menu_items';
try {
    $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
    echo "Columns in {$table}:" . PHP_EOL;
    print_r($columns);

    if (in_array('deleted_at', $columns)) {
        echo "deleted_at column exists." . PHP_EOL;
    } else {
        echo "deleted_at column is MISSING." . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
