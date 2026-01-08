
// ERP Tables to sync directly
$tables = [
    'crm_users',
    'crm_branches',
    'crm_categories',
    'crm_products',
    'crm_inventories',
    'crm_inventory_adjustments',
];

foreach ($tables as $table) {
    try {
        $count = 0;
        foreach (DB::table($table)->get() as $row) {
            $data = (array)$row;
            DB::connection('supabase')->table($table)->updateOrInsert(
                ['id' => $data['id']],
                $data
            );
            $count++;
        }
        echo "Synced $count records for table $table to Supabase\n";
    } catch (\Exception $e) {
        echo "Error for table $table: " . $e->getMessage() . "\n";
    }
}

// Re-dispatch Orders
foreach (App\Models\Order::all() as $order) {
    App\Jobs\SyncOrderToSupabase::dispatch($order->id);
}
echo "Dispatched " . App\Models\Order::count() . " ERP order sync jobs\n";