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
            'users',
            'branches',
            'categories',
            'menu_items',
            'menu_item_ingredients',
            'menu_item_branch',
            'tables',
            'orders',
            'order_items',
            'payments',
            'inventories',
            'inventory_adjustments',
            'activities',
            'void_requests',
            'shift_reports',
            'deleted_data',
        ];

        // Enable RLS on all tables
        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY;");
        }

        // =====================================================
        // USERS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY users_select_policy ON users
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY users_insert_policy ON users
            FOR INSERT
            WITH CHECK (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY users_update_policy ON users
            FOR UPDATE
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY users_delete_policy ON users
            FOR DELETE
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        // =====================================================
        // BRANCHES TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY branches_select_policy ON branches
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY branches_modify_policy ON branches
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        // =====================================================
        // CATEGORIES TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY categories_select_policy ON categories
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY categories_modify_policy ON categories
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // MENU_ITEMS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY menu_items_select_policy ON menu_items
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY menu_items_modify_policy ON menu_items
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // MENU_ITEM_INGREDIENTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY menu_item_ingredients_select_policy ON menu_item_ingredients
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY menu_item_ingredients_modify_policy ON menu_item_ingredients
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'inventory')
                )
            );
        ");

        // =====================================================
        // MENU_ITEM_BRANCH TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY menu_item_branch_select_policy ON menu_item_branch
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY menu_item_branch_modify_policy ON menu_item_branch
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // TABLES TABLE POLICIES (Restaurant Tables)
        // =====================================================
        DB::statement("
            CREATE POLICY tables_select_policy ON tables
            FOR SELECT
            USING (
                branch_id = (
                    SELECT branch_id FROM users 
                    WHERE id = current_setting('app.user_id')::bigint
                )
                OR EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY tables_modify_policy ON tables
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // ORDERS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY orders_select_policy ON orders
            FOR SELECT
            USING (
                branch_id = (
                    SELECT branch_id FROM users 
                    WHERE id = current_setting('app.user_id')::bigint
                )
                OR EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY orders_insert_policy ON orders
            FOR INSERT
            WITH CHECK (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        DB::statement("
            CREATE POLICY orders_update_policy ON orders
            FOR UPDATE
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        DB::statement("
            CREATE POLICY orders_delete_policy ON orders
            FOR DELETE
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // ORDER_ITEMS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY order_items_select_policy ON order_items
            FOR SELECT
            USING (
                EXISTS (
                    SELECT 1 FROM orders 
                    WHERE orders.id = order_items.order_id
                    AND (
                        orders.branch_id = (
                            SELECT branch_id FROM users 
                            WHERE id = current_setting('app.user_id')::bigint
                        )
                        OR EXISTS (
                            SELECT 1 FROM users 
                            WHERE id = current_setting('app.user_id')::bigint 
                            AND role = 'admin'
                        )
                    )
                )
            );
        ");

        DB::statement("
            CREATE POLICY order_items_modify_policy ON order_items
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        // =====================================================
        // PAYMENTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY payments_select_policy ON payments
            FOR SELECT
            USING (
                EXISTS (
                    SELECT 1 FROM orders 
                    WHERE orders.id = payments.order_id
                    AND (
                        orders.branch_id = (
                            SELECT branch_id FROM users 
                            WHERE id = current_setting('app.user_id')::bigint
                        )
                        OR EXISTS (
                            SELECT 1 FROM users 
                            WHERE id = current_setting('app.user_id')::bigint 
                            AND role = 'admin'
                        )
                    )
                )
            );
        ");

        DB::statement("
            CREATE POLICY payments_modify_policy ON payments
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        // =====================================================
        // INVENTORIES TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY inventories_select_policy ON inventories
            FOR SELECT
            USING (
                branch_id = (
                    SELECT branch_id FROM users 
                    WHERE id = current_setting('app.user_id')::bigint
                )
                OR EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role = 'admin'
                )
            );
        ");

        DB::statement("
            CREATE POLICY inventories_modify_policy ON inventories
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'inventory')
                )
            );
        ");

        // =====================================================
        // INVENTORY_ADJUSTMENTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY inventory_adjustments_select_policy ON inventory_adjustments
            FOR SELECT
            USING (
                EXISTS (
                    SELECT 1 FROM inventories 
                    WHERE inventories.id = inventory_adjustments.inventory_id
                    AND (
                        inventories.branch_id = (
                            SELECT branch_id FROM users 
                            WHERE id = current_setting('app.user_id')::bigint
                        )
                        OR EXISTS (
                            SELECT 1 FROM users 
                            WHERE id = current_setting('app.user_id')::bigint 
                            AND role = 'admin'
                        )
                    )
                )
            );
        ");


        DB::statement("
            CREATE POLICY inventory_adjustments_modify_policy ON inventory_adjustments
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'inventory')
                )
            );
        ");

        // =====================================================
        // ACTIVITIES TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY activities_select_policy ON activities
            FOR SELECT
            USING (true);
        ");

        DB::statement("
            CREATE POLICY activities_insert_policy ON activities
            FOR INSERT
            WITH CHECK (true);
        ");

        // =====================================================
        // VOID_REQUESTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY void_requests_select_policy ON void_requests
            FOR SELECT
            USING (
                EXISTS (
                    SELECT 1 FROM orders 
                    WHERE orders.id = void_requests.order_id
                    AND (
                        orders.branch_id = (
                            SELECT branch_id FROM users 
                            WHERE id = current_setting('app.user_id')::bigint
                        )
                        OR EXISTS (
                            SELECT 1 FROM users 
                            WHERE id = current_setting('app.user_id')::bigint 
                            AND role = 'admin'
                        )
                    )
                )
            );
        ");


        DB::statement("
            CREATE POLICY void_requests_insert_policy ON void_requests
            FOR INSERT
            WITH CHECK (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager', 'cashier')
                )
            );
        ");

        DB::statement("
            CREATE POLICY void_requests_update_policy ON void_requests
            FOR UPDATE
            USING (
                EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // SHIFT_REPORTS TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY shift_reports_select_policy ON shift_reports
            FOR SELECT
            USING (
                user_id = current_setting('app.user_id')::bigint
                OR branch_id = (
                    SELECT branch_id FROM users 
                    WHERE id = current_setting('app.user_id')::bigint
                )
                OR EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        DB::statement("
            CREATE POLICY shift_reports_modify_policy ON shift_reports
            FOR ALL
            USING (
                user_id = current_setting('app.user_id')::bigint
                OR EXISTS (
                    SELECT 1 FROM users 
                    WHERE id = current_setting('app.user_id')::bigint 
                    AND role IN ('admin', 'manager')
                )
            );
        ");

        // =====================================================
        // DELETED_DATA TABLE POLICIES
        // =====================================================
        DB::statement("
            CREATE POLICY deleted_data_admin_only ON deleted_data
            FOR ALL
            USING (
                EXISTS (
                    SELECT 1 FROM users 
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

        // List of tables to disable RLS
        $tables = [
            'users',
            'branches',
            'categories',
            'menu_items',
            'menu_item_ingredients',
            'menu_item_branch',
            'tables',
            'orders',
            'order_items',
            'payments',
            'inventories',
            'inventory_adjustments',
            'activities',
            'void_requests',
            'shift_reports',
            'deleted_data',
        ];

        // Drop all policies and disable RLS
        foreach ($tables as $table) {
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
