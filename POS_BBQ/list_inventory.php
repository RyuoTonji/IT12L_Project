<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inventory;

$items = Inventory::all(['id', 'name', 'category']);
echo "ID | Name | Category\n";
echo str_repeat("-", 60) . "\n";
foreach ($items as $item) {
    echo str_pad($item->id, 3) . " | " . str_pad($item->name, 30) . " | " . $item->category . "\n";
}
echo str_repeat("-", 60) . "\n";
echo "Total count: " . count($items) . "\n";
