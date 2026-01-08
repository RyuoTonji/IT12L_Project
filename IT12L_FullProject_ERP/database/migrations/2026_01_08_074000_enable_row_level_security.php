<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // List of ERP tables to enable RLS on
        $tables = [
            'crm_users',
            'crm_branches',
            'crm_categories',
            'crm_products',
            'crm_carts',
            'crm_cart_items',
            'crm_orders',
            'crm_order_items',
            'crm_inventories',
            'crm_inventory_adjustments',
            'crm_feedbacks',
            'crm_deletion_logs',
        ];

        // Enable RLS on all tables
        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY;");
        }

        // Basic policies: Admins can do everything, others can select public data
        // Note: For now we allow SELECT to all to ensure cross-project visibility for forecasting
        foreach ($tables as $table) {
            DB::statement("DROP POLICY IF EXISTS {$table}_select_policy ON {$table};");
            DB::statement("CREATE POLICY {$table}_select_policy ON {$table} FOR SELECT USING (true);");
        }

        // Restrict modifications to admin users (simplified for this update)
        $adminRestricted = [
            'crm_branches',
            'crm_categories',
            'crm_products',
            'crm_inventories',
        ];

        foreach ($adminRestricted as $table) {
            DB::statement("DROP POLICY IF EXISTS {$table}_modify_policy ON {$table};");
            DB::statement("
                CREATE POLICY {$table}_modify_policy ON {$table}
                FOR ALL
                TO authenticated
                USING (
                    EXISTS (
                        SELECT 1 FROM crm_users 
                        WHERE id = current_setting('app.user_id', true)::bigint 
                        AND role = 'admin'
                    )
                );
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'crm_users',
            'crm_branches',
            'crm_categories',
            'crm_products',
            'crm_carts',
            'crm_cart_items',
            'crm_orders',
            'crm_order_items',
            'crm_inventories',
            'crm_inventory_adjustments',
            'crm_feedbacks',
            'crm_deletion_logs',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} DISABLE ROW LEVEL SECURITY;");
            DB::statement("DROP POLICY IF EXISTS {$table}_select_policy ON {$table};");
            DB::statement("DROP POLICY IF EXISTS {$table}_modify_policy ON {$table};");
        }
    }
};
