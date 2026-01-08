<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tablesToRename = [
    'users' => 'pos_users',
    'branches' => 'pos_branches',
    'categories' => 'pos_categories',
    'menu_items' => 'pos_menu_items',
    'tables' => 'pos_tables',
    'orders' => 'pos_orders',
    'order_items' => 'pos_order_items',
    'payments' => 'pos_payments',
    'inventories' => 'pos_inventories',
    'activities' => 'pos_activities',
    'deleted_data' => 'pos_deleted_data',
    'void_requests' => 'pos_void_requests',
    'shift_reports' => 'pos_shift_reports',
    'menu_item_ingredients' => 'pos_menu_item_ingredients',
    'inventory_adjustments' => 'pos_inventory_adjustments',
    'menu_item_branch' => 'pos_menu_item_branch',
    'cache' => 'pos_cache',
    'cache_locks' => 'pos_cache_locks',
    'jobs' => 'pos_jobs',
    'failed_jobs' => 'pos_failed_jobs',
    'job_batches' => 'pos_job_batches',
    'sessions' => 'pos_sessions',
    'password_reset_tokens' => 'pos_password_reset_tokens',
    'migrations' => 'pos_migrations',
];

foreach ($tablesToRename as $old => $new) {
    try {
        echo "Renaming {$old} to {$new}... ";
        // Use raw query for renaming in PostgreSQL/Supabase
        DB::connection('supabase')->statement("ALTER TABLE \"{$old}\" RENAME TO \"{$new}\"");
        echo "Done." . PHP_EOL;
    } catch (Exception $e) {
        echo "Failed: " . $e->getMessage() . PHP_EOL;
    }
}
