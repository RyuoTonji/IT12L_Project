<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$models = [
    App\Models\Branch::class,
    App\Models\Category::class,
    App\Models\MenuItem::class,
    App\Models\Order::class,
    App\Models\OrderItem::class,
    App\Models\Payment::class,
    App\Models\Inventory::class,
    App\Models\Table::class,
    App\Models\User::class,
    App\Models\VoidRequest::class,
    App\Models\ShiftReport::class,
    App\Models\Activity::class,
];

foreach ($models as $modelClass) {
    try {
        $count = 0;
        foreach ($modelClass::all() as $model) {
            App\Jobs\SyncModelToSupabase::dispatch(
                $modelClass,
                $model->getAttributes(),
                $model->getKey(),
                'save'
            );
            $count++;
        }
        echo "Dispatched $count sync jobs for $modelClass\n";
    } catch (\Exception $e) {
        echo "Error for $modelClass: " . $e->getMessage() . "\n";
    }
}
