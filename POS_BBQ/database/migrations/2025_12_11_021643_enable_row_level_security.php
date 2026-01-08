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

        // List of tables to enable RLS on (excluding Laravel system tables)
        $tables = [
            'pos_users',
            'pos_branches',
            'pos_categories',
            'pos_menu_items',
            'pos_menu_item_ingredients',
            'pos_menu_item_branch',
            'pos_tables',
            'pos_orders',
            'pos_order_items',
            'pos_payments',
            'pos_inventory',
            'pos_inventory_adjustments',
            'pos_activities',
            'pos_void_requests',
            'pos_shift_reports',
            'pos_deleted_data',
        ];

        // Enable RLS on all tables
        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY;");
        }

        // =====================================================
        // USERS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_users_select_policy ON pos_users
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY pos_users_insert_policy ON pos_users
            FOR INSERT
            WITH CHECK (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_users_update_policy ON pos_users
            FOR UPDATE
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_users_delete_policy ON pos_users
            FOR DELETE
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        // =====================================================
        // BRANCHES TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_branches_select_policy ON pos_branches
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY pos_branches_modify_policy ON pos_branches
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        // =====================================================
        // CATEGORIES TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_categories_select_policy ON pos_categories
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY pos_categories_modify_policy ON pos_categories
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // MENU_ITEMS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_menu_items_select_policy ON pos_menu_items
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY pos_menu_items_modify_policy ON pos_menu_items
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // MENU_ITEM_INGREDIENTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_menu_item_ingredients_select_policy ON pos_menu_item_ingredients
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY pos_menu_item_ingredients_modify_policy ON pos_menu_item_ingredients
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'inventory')
                )
            );
        ");

        // =====================================================
        // MENU_ITEM_BRANCH TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_menu_item_branch_select_policy ON pos_menu_item_branch
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY pos_menu_item_branch_modify_policy ON pos_menu_item_branch
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // TABLES TABLE POLICIES (Restaurant Tables)
        // =====================================================
        DB::statement("
            CREATE POLICY pos_tables_select_policy ON pos_tables
            FOR SELECT
            USING (
                branch_id = (
                    SELECT branch_id FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint
                )
                OR EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_tables_modify_policy ON pos_tables
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // ORDERS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_orders_select_policy ON pos_orders
            FOR SELECT
            USING (
                branch_id = (
                    SELECT branch_id FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint
                )
                OR EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_orders_insert_policy ON pos_orders
            FOR INSERT
            WITH CHECK (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_orders_update_policy ON pos_orders
            FOR UPDATE
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_orders_delete_policy ON pos_orders
            FOR DELETE
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // ORDER_ITEMS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_order_items_select_policy ON pos_order_items
            FOR SELECT
            USING (
                EXISTS (
                    SELECT 1 FROM pos_orders 
                    WHERE pos_orders.id = pos_order_items.order_id
                    AND (
                        pos_orders.branch_id = (
                            SELECT branch_id FROM pos_users 
                            WHERE id = current_setting('app.user_id')::bigint
                        )
                        OR EXISTS (
                            SELECT 1 FROM pos_users 
                            WHERE id = current_setting('app.user_id')::bigint 
                            AND role = 'admin'
                        )
                    )
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_order_items_modify_policy ON pos_order_items
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        // =====================================================
        // PAYMENTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_payments_select_policy ON pos_payments
            FOR SELECT
            USING (
                EXISTS (
                    SELECT 1 FROM pos_orders 
                    WHERE pos_orders.id = pos_payments.order_id
                    AND (
                        pos_orders.branch_id = (
                            SELECT branch_id FROM pos_users 
                            WHERE id = current_setting('app.user_id')::bigint
                        )
                        OR EXISTS (
                            SELECT 1 FROM pos_users 
                            WHERE id = current_setting('app.user_id')::bigint 
                            AND role = 'admin'
                        )
                    )
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_payments_modify_policy ON pos_payments
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        // =====================================================
        // INVENTORIES TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_inventory_select_policy ON pos_inventory
            FOR SELECT
            USING (
                branch_id = (
                    SELECT branch_id FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint
                )
                OR EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_inventory_modify_policy ON pos_inventory
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'inventory')
                )
            );
        ");

        // =====================================================
        // INVENTORY_ADJUSTMENTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_inventory_adjustments_select_policy ON pos_inventory_adjustments
            FOR SELECT
            USING (
                EXISTS (
                    SELECT 1 FROM pos_inventory 
                    WHERE pos_inventory.id = pos_inventory_adjustments.inventory_id
                    AND (
                        pos_inventory.branch_id = (
                            SELECT branch_id FROM pos_users 
                            WHERE id = current_setting('app.user_id')::bigint
                        )
                        OR EXISTS (
                            SELECT 1 FROM pos_users 
                            WHERE id = current_setting('app.user_id')::bigint 
                            AND role = 'admin'
                        )
                    )
                )
            );
        ");


        DB::statement("
            CREATE POLICY pos_inventory_adjustments_modify_policy ON pos_inventory_adjustments
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'inventory')
                )
            );
        ");

        // =====================================================
        // ACTIVITIES TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_activities_select_policy ON pos_activities
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY pos_activities_insert_policy ON pos_activities
            FOR INSERT
            WITH CHECK (true);
        ");

        // =====================================================
        // VOID_REQUESTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_void_requests_select_policy ON pos_void_requests
            FOR SELECT
            USING (
                EXISTS (
                    SELECT 1 FROM pos_orders 
                    WHERE pos_orders.id = pos_void_requests.order_id
                    AND (
                        pos_orders.branch_id = (
                            SELECT branch_id FROM pos_users 
                            WHERE id = current_setting('app.user_id')::bigint
                        )
                        OR EXISTS (
                            SELECT 1 FROM pos_users 
                            WHERE id = current_setting('app.user_id')::bigint 
                            AND role = 'admin'
                        )
                    )
                )
            );
        ");


        DB::statement("
            CREATE POLICY pos_void_requests_insert_policy ON pos_void_requests
            FOR INSERT
            WITH CHECK (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_void_requests_update_policy ON pos_void_requests
            FOR UPDATE
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // SHIFT_REPORTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_shift_reports_select_policy ON pos_shift_reports
            FOR SELECT
            USING (
                user_id = current_setting('app.user_id')::bigint
                OR branch_id = (
                    SELECT branch_id FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint
                )
                OR EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        DB::statement("
            CREATE POLICY pos_shift_reports_modify_policy ON pos_shift_reports
            FOR ALL
            USING (
                user_id = current_setting('app.user_id')::bigint
                OR EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // DELETED_DATA TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY pos_deleted_data_admin_only ON pos_deleted_data
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM pos_users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // List of pos_tables to disable RLS
        $pos_tables = [
            'pos_users',
            'pos_branches',
            'pos_categories',
            'pos_menu_items',
            'pos_menu_item_ingredients',
            'pos_menu_item_branch',
            'pos_tables',
            'pos_orders',
            'pos_order_items',
            'pos_payments',
            'pos_inventory',
            'pos_inventory_adjustments',
            'pos_activities',
            'pos_void_requests',
            'pos_shift_reports',
            'pos_deleted_data',
        ];

        // Drop all policies and disable RLS
        foreach ($pos_tables as $table) {
            // Drop all policies for the table
            $policies = DB::select("
                SELECT policyname 
                FROM pg_policies 
                WHERE tablename = ?
            ", [$table]);

            foreach ($policies as $policy) {
                DB::statement("DROP POLICY IF EXISTS {$policy->policyname} ON {$table};");
            }

            // Disable RLS
            DB::statement("ALTER TABLE {$table} DISABLE ROW LEVEL SECURITY;");
        }

    }
};
