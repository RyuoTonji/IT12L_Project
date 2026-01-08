<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$table = $argv[1] ?? 'migrations';

try {
    $rows = Illuminate\Support\Facades\DB::connection('supabase')->table($table)->get();
    echo "Rows in {$table}:" . PHP_EOL;
    foreach ($rows as $row) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
