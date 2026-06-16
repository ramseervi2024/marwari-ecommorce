<?php
namespace RestaurantManagementApi\Database;

class Migrations {
    
    /**
     * Set up DB tables, custom roles, and test seeds
     */
    public static function activate() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        $charset_collate = $wpdb->get_charset_collate();

        // 1. Tables Table
        $table_tables = $wpdb->prefix . 'restaurant_tables';
        $sql_tables = "CREATE TABLE $table_tables (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            table_number varchar(50) NOT NULL,
            capacity int(11) NOT NULL DEFAULT 4,
            floor varchar(50) DEFAULT 'Ground',
            status varchar(50) NOT NULL DEFAULT 'Available',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY table_number (table_number)
        ) $charset_collate;";
        dbDelta($sql_tables);

        // 2. Categories Table
        $table_categories = $wpdb->prefix . 'restaurant_categories';
        $sql_categories = "CREATE TABLE $table_categories (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY name (name)
        ) $charset_collate;";
        dbDelta($sql_categories);

        // 3. Menu Items Table
        $table_menu = $wpdb->prefix . 'restaurant_menu';
        $sql_menu = "CREATE TABLE $table_menu (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            item_code varchar(50) NOT NULL,
            item_name varchar(255) NOT NULL,
            category_id bigint(20) NOT NULL,
            description text DEFAULT NULL,
            price decimal(10,2) NOT NULL DEFAULT 0.00,
            cost_price decimal(10,2) NOT NULL DEFAULT 0.00,
            tax_percentage decimal(5,2) NOT NULL DEFAULT 5.00,
            preparation_time int(11) NOT NULL DEFAULT 15,
            image varchar(255) DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'Available',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY item_code (item_code)
        ) $charset_collate;";
        dbDelta($sql_menu);

        // 4. Orders Table
        $table_orders = $wpdb->prefix . 'restaurant_orders';
        $sql_orders = "CREATE TABLE $table_orders (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_number varchar(50) NOT NULL,
            table_id bigint(20) DEFAULT NULL,
            waiter_id bigint(20) DEFAULT NULL,
            customer_name varchar(100) DEFAULT NULL,
            subtotal decimal(10,2) NOT NULL DEFAULT 0.00,
            discount decimal(10,2) NOT NULL DEFAULT 0.00,
            tax decimal(10,2) NOT NULL DEFAULT 0.00,
            total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(50) NOT NULL DEFAULT 'Pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY order_number (order_number)
        ) $charset_collate;";
        dbDelta($sql_orders);

        // 5. Order Line Items Table
        $table_order_items = $wpdb->prefix . 'restaurant_order_items';
        $sql_order_items = "CREATE TABLE $table_order_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_id bigint(20) NOT NULL,
            menu_item_id bigint(20) NOT NULL,
            quantity int(11) NOT NULL DEFAULT 1,
            price decimal(10,2) NOT NULL,
            tax decimal(10,2) NOT NULL DEFAULT 0.00,
            total decimal(10,2) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_order_items);

        // 6. Invoices Table
        $table_invoices = $wpdb->prefix . 'restaurant_invoices';
        $sql_invoices = "CREATE TABLE $table_invoices (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_number varchar(50) NOT NULL,
            order_id bigint(20) NOT NULL,
            customer_id bigint(20) DEFAULT NULL,
            subtotal decimal(10,2) NOT NULL DEFAULT 0.00,
            discount decimal(10,2) NOT NULL DEFAULT 0.00,
            tax decimal(10,2) NOT NULL DEFAULT 0.00,
            service_charge decimal(10,2) NOT NULL DEFAULT 0.00,
            total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            payment_method varchar(50) NOT NULL DEFAULT 'Cash',
            status varchar(50) NOT NULL DEFAULT 'Unpaid',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY invoice_number (invoice_number)
        ) $charset_collate;";
        dbDelta($sql_invoices);

        // 7. Customers Table
        $table_customers = $wpdb->prefix . 'restaurant_customers';
        $sql_customers = "CREATE TABLE $table_customers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            address varchar(255) DEFAULT NULL,
            loyalty_points int(11) NOT NULL DEFAULT 0,
            total_orders int(11) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_customers);

        // 8. Ingredients Table
        $table_ingredients = $wpdb->prefix . 'restaurant_ingredients';
        $sql_ingredients = "CREATE TABLE $table_ingredients (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ingredient_name varchar(100) NOT NULL,
            unit varchar(50) NOT NULL DEFAULT 'kg',
            current_stock decimal(10,2) NOT NULL DEFAULT 0.00,
            minimum_stock decimal(10,2) NOT NULL DEFAULT 1.00,
            purchase_price decimal(10,2) NOT NULL DEFAULT 0.00,
            supplier_id bigint(20) DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'Active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_ingredients);

        // 9. Recipes Table
        $table_recipes = $wpdb->prefix . 'restaurant_recipes';
        $sql_recipes = "CREATE TABLE $table_recipes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            menu_item_id bigint(20) NOT NULL,
            ingredient_id bigint(20) NOT NULL,
            quantity_required decimal(10,4) NOT NULL DEFAULT 0.0000,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_recipes);

        // 10. Suppliers Table
        $table_suppliers = $wpdb->prefix . 'restaurant_suppliers';
        $sql_suppliers = "CREATE TABLE $table_suppliers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            supplier_name varchar(100) NOT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            gst_number varchar(50) DEFAULT NULL,
            address varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_suppliers);

        // 11. Purchases Table
        $table_purchases = $wpdb->prefix . 'restaurant_purchases';
        $sql_purchases = "CREATE TABLE $table_purchases (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            supplier_id bigint(20) NOT NULL,
            total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(50) NOT NULL DEFAULT 'Pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_purchases);

        // 12. Purchase Items Table
        $table_purchase_items = $wpdb->prefix . 'restaurant_purchase_items';
        $sql_purchase_items = "CREATE TABLE $table_purchase_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            purchase_id bigint(20) NOT NULL,
            ingredient_id bigint(20) NOT NULL,
            quantity decimal(10,2) NOT NULL,
            price decimal(10,2) NOT NULL,
            total decimal(10,2) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_purchase_items);

        // 13. Deliveries Table
        $table_deliveries = $wpdb->prefix . 'restaurant_deliveries';
        $sql_deliveries = "CREATE TABLE $table_deliveries (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_id bigint(20) NOT NULL,
            customer_address text NOT NULL,
            delivery_partner varchar(100) DEFAULT NULL,
            delivery_charge decimal(10,2) NOT NULL DEFAULT 0.00,
            delivery_status varchar(50) NOT NULL DEFAULT 'Assigned',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_deliveries);

        // 14. Branches Table
        $table_branches = $wpdb->prefix . 'restaurant_branches';
        $sql_branches = "CREATE TABLE $table_branches (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            branch_name varchar(100) NOT NULL,
            branch_code varchar(50) NOT NULL,
            address varchar(255) DEFAULT NULL,
            manager varchar(100) DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'Active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY branch_code (branch_code)
        ) $charset_collate;";
        dbDelta($sql_branches);

        // 15. Staff Shifts Table
        $table_staff = $wpdb->prefix . 'restaurant_staff';
        $sql_staff = "CREATE TABLE $table_staff (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            role varchar(50) NOT NULL,
            shift_start varchar(50) DEFAULT '09:00',
            shift_end varchar(50) DEFAULT '17:00',
            salary decimal(10,2) NOT NULL DEFAULT 0.00,
            attendance_status varchar(50) NOT NULL DEFAULT 'Absent',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_staff);

        // 16. Expenses Table
        $table_expenses = $wpdb->prefix . 'restaurant_expenses';
        $sql_expenses = "CREATE TABLE $table_expenses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            expense_type varchar(100) NOT NULL DEFAULT 'Miscellaneous',
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_expenses);

        // 17. Activity Logs Table
        $table_logs = $wpdb->prefix . 'restaurant_activity_logs';
        $sql_logs = "CREATE TABLE $table_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            action varchar(100) NOT NULL,
            details text DEFAULT NULL,
            ip_address varchar(100) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_logs);

        // Set up Custom Roles & seeds
        self::register_roles();
        self::seed_initial_records();
    }

    /**
     * Register roles
     */
    private static function register_roles() {
        remove_role('restaurant_super_admin');
        remove_role('restaurant_manager');
        remove_role('restaurant_cashier');
        remove_role('restaurant_chef');
        remove_role('restaurant_waiter');
        remove_role('restaurant_delivery');

        $super_admin_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_restaurant' => true,
            'manage_users' => true,
            'manage_orders' => true,
            'manage_inventory' => true,
            'manage_staff' => true,
            'view_reports' => true,
        ];

        $manager_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_orders' => true,
            'manage_inventory' => true,
            'manage_staff' => true,
            'view_reports' => true,
        ];

        $cashier_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_billing' => true,
            'manage_orders' => true,
        ];

        $chef_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_kitchen' => true,
        ];

        $waiter_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_orders' => true,
        ];

        $delivery_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_deliveries' => true,
        ];

        add_role('restaurant_super_admin', 'Restaurant Super Admin', $super_admin_caps);
        add_role('restaurant_manager', 'Restaurant Manager', $manager_caps);
        add_role('restaurant_cashier', 'Restaurant Cashier', $cashier_caps);
        add_role('restaurant_chef', 'Restaurant Chef', $chef_caps);
        add_role('restaurant_waiter', 'Restaurant Waiter', $waiter_caps);
        add_role('restaurant_delivery', 'Restaurant Delivery Executive', $delivery_caps);

        // Setup administrator
        $admin = get_role('administrator');
        if ($admin) {
            foreach ($super_admin_caps as $cap => $grant) {
                $admin->add_cap($cap);
            }
        }
    }

    /**
     * Seeds initial records
     */
    private static function seed_initial_records() {
        // Users
        $super_admin_id = username_exists('restsuperadmin');
        if ($super_admin_id) {
            wp_set_password('123456', $super_admin_id);
            $user = get_userdata($super_admin_id);
            if (!in_array('restaurant_super_admin', $user->roles)) {
                $user->set_role('restaurant_super_admin');
            }
            update_user_meta($super_admin_id, 'restaurant_user_status', 'APPROVED');
        } else {
            $user_id = wp_insert_user([
                'user_login' => 'restsuperadmin',
                'user_email' => 'admin@restaurant.erp',
                'user_pass' => '123456',
                'display_name' => 'Restaurant Super Admin',
                'first_name' => 'Restaurant',
                'last_name' => 'Super Admin',
                'role' => 'restaurant_super_admin'
            ]);
            if (!is_wp_error($user_id)) {
                update_user_meta($user_id, 'restaurant_user_status', 'APPROVED');
            }
        }

        self::create_test_user('rest_manager', 'manager@restaurant.erp', 'managerpass123', 'Restaurant Manager', 'restaurant_manager');
        self::create_test_user('rest_cashier', 'cashier@restaurant.erp', 'cashierpass123', 'Restaurant Cashier', 'restaurant_cashier');
        self::create_test_user('rest_chef', 'chef@restaurant.erp', 'chefpass123', 'Restaurant Chef', 'restaurant_chef');
        self::create_test_user('rest_waiter', 'waiter@restaurant.erp', 'waiterpass123', 'Restaurant Waiter', 'restaurant_waiter');
        self::create_test_user('rest_delivery', 'delivery@restaurant.erp', 'deliverypass123', 'Restaurant Delivery', 'restaurant_delivery');

        // Seed Tables, Categories, Menu Items, Ingredients, Suppliers
        global $wpdb;
        $t_tables = $wpdb->prefix . 'restaurant_tables';
        $t_categories = $wpdb->prefix . 'restaurant_categories';
        $t_menu = $wpdb->prefix . 'restaurant_menu';
        $t_ingredients = $wpdb->prefix . 'restaurant_ingredients';
        $t_recipes = $wpdb->prefix . 'restaurant_recipes';
        $t_suppliers = $wpdb->prefix . 'restaurant_suppliers';

        // Check if tables are already seeded
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $t_tables")) === 0) {
            $wpdb->insert($t_tables, ['table_number' => 'Table 1', 'capacity' => 2, 'floor' => 'Ground', 'status' => 'Available']);
            $wpdb->insert($t_tables, ['table_number' => 'Table 2', 'capacity' => 4, 'floor' => 'Ground', 'status' => 'Available']);
            $wpdb->insert($t_tables, ['table_number' => 'Table 3', 'capacity' => 4, 'floor' => 'Ground', 'status' => 'Occupied']);
            $wpdb->insert($t_tables, ['table_number' => 'Table 4', 'capacity' => 6, 'floor' => 'First Floor', 'status' => 'Reserved']);
            $wpdb->insert($t_tables, ['table_number' => 'Table 5', 'capacity' => 2, 'floor' => 'First Floor', 'status' => 'Cleaning']);
        }

        // Categories
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $t_categories")) === 0) {
            $wpdb->insert($t_categories, ['name' => 'Starters']);
            $wpdb->insert($t_categories, ['name' => 'Main Course']);
            $wpdb->insert($t_categories, ['name' => 'Desserts']);
            $wpdb->insert($t_categories, ['name' => 'Beverages']);
        }

        $starter_id = $wpdb->get_var("SELECT id FROM $t_categories WHERE name='Starters'");
        $main_id = $wpdb->get_var("SELECT id FROM $t_categories WHERE name='Main Course'");
        $dessert_id = $wpdb->get_var("SELECT id FROM $t_categories WHERE name='Desserts'");
        $bev_id = $wpdb->get_var("SELECT id FROM $t_categories WHERE name='Beverages'");

        // Suppliers
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $t_suppliers")) === 0) {
            $wpdb->insert($t_suppliers, [
                'supplier_name' => 'F&B Fresh Grocer',
                'mobile' => '9876543210',
                'email' => 'sales@fbfresh.com',
                'gst_number' => '27ABCDE1234F1Z5',
                'address' => 'Market Yard, Pune'
            ]);
        }
        $supplier_id = $wpdb->get_var("SELECT id FROM $t_suppliers LIMIT 1");

        // Ingredients
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $t_ingredients")) === 0) {
            $wpdb->insert($t_ingredients, ['ingredient_name' => 'Chicken Breast', 'unit' => 'kg', 'current_stock' => 15.00, 'minimum_stock' => 5.00, 'purchase_price' => 250.00, 'supplier_id' => $supplier_id]);
            $wpdb->insert($t_ingredients, ['ingredient_name' => 'Basmati Rice', 'unit' => 'kg', 'current_stock' => 50.00, 'minimum_stock' => 10.00, 'purchase_price' => 90.00, 'supplier_id' => $supplier_id]);
            $wpdb->insert($t_ingredients, ['ingredient_name' => 'Paneer', 'unit' => 'kg', 'current_stock' => 8.00, 'minimum_stock' => 3.00, 'purchase_price' => 320.00, 'supplier_id' => $supplier_id]);
            $wpdb->insert($t_ingredients, ['ingredient_name' => 'Onion', 'unit' => 'kg', 'current_stock' => 30.00, 'minimum_stock' => 10.00, 'purchase_price' => 25.00, 'supplier_id' => $supplier_id]);
            $wpdb->insert($t_ingredients, ['ingredient_name' => 'Tomato', 'unit' => 'kg', 'current_stock' => 20.00, 'minimum_stock' => 8.00, 'purchase_price' => 40.00, 'supplier_id' => $supplier_id]);
        }
        $ing_chicken = $wpdb->get_var("SELECT id FROM $t_ingredients WHERE ingredient_name='Chicken Breast'");
        $ing_rice = $wpdb->get_var("SELECT id FROM $t_ingredients WHERE ingredient_name='Basmati Rice'");
        $ing_paneer = $wpdb->get_var("SELECT id FROM $t_ingredients WHERE ingredient_name='Paneer'");

        // Menu Items
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $t_menu")) === 0) {
            $wpdb->insert($t_menu, [
                'item_code' => 'M001',
                'item_name' => 'Paneer Tikka',
                'category_id' => $starter_id,
                'description' => 'Marinated paneer chunks grilled in tandoor.',
                'price' => 220.00,
                'cost_price' => 95.00,
                'tax_percentage' => 5.00,
                'preparation_time' => 15,
                'status' => 'Available'
            ]);
            $paneer_item_id = $wpdb->insert_id;
            
            $wpdb->insert($t_menu, [
                'item_code' => 'M002',
                'item_name' => 'Chicken Biryani',
                'category_id' => $main_id,
                'description' => 'Aromatic basmati rice cooked with tender chicken and spices.',
                'price' => 350.00,
                'cost_price' => 140.00,
                'tax_percentage' => 5.00,
                'preparation_time' => 25,
                'status' => 'Available'
            ]);
            $biryani_item_id = $wpdb->insert_id;

            $wpdb->insert($t_menu, [
                'item_code' => 'M003',
                'item_name' => 'Chocolate Lava Cake',
                'category_id' => $dessert_id,
                'description' => 'Hot chocolate cake with a molten center.',
                'price' => 180.00,
                'cost_price' => 60.00,
                'tax_percentage' => 5.00,
                'preparation_time' => 10,
                'status' => 'Available'
            ]);

            $wpdb->insert($t_menu, [
                'item_code' => 'M004',
                'item_name' => 'Fresh Lime Soda',
                'category_id' => $bev_id,
                'description' => 'Tangy fresh lime with soda or water.',
                'price' => 90.00,
                'cost_price' => 20.00,
                'tax_percentage' => 5.00,
                'preparation_time' => 5,
                'status' => 'Available'
            ]);

            // Recipes (Ing. linkages)
            if ($paneer_item_id && $ing_paneer) {
                $wpdb->insert($t_recipes, ['menu_item_id' => $paneer_item_id, 'ingredient_id' => $ing_paneer, 'quantity_required' => 0.2000]); // 200g
            }
            if ($biryani_item_id && $ing_chicken && $ing_rice) {
                $wpdb->insert($t_recipes, ['menu_item_id' => $biryani_item_id, 'ingredient_id' => $ing_chicken, 'quantity_required' => 0.2500]); // 250g
                $wpdb->insert($t_recipes, ['menu_item_id' => $biryani_item_id, 'ingredient_id' => $ing_rice, 'quantity_required' => 0.1500]); // 150g
            }
        }
    }

    private static function create_test_user(string $username, string $email, string $password, string $display_name, string $role) {
        $user_id = username_exists($username);
        if (!$user_id && !email_exists($email)) {
            $user_id = wp_insert_user([
                'user_login' => $username,
                'user_email' => $email,
                'user_pass' => $password,
                'display_name' => $display_name,
                'first_name' => $display_name,
                'role' => $role
            ]);
        }
        if ($user_id && !is_wp_error($user_id)) {
            update_user_meta($user_id, 'restaurant_user_status', 'APPROVED');
        }
    }
}
