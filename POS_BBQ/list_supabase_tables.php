<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tables = Illuminate\Support\Facades\DB::connection('supabase')->select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    foreach ($tables as $t) {
        echo $t->table_name . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
