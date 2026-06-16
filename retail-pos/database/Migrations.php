<?php
namespace RetailPosApi\Database;

class Migrations {
    
    /**
     * Activate migrations - Creates tables & user roles
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // 1. JWT Secret Setup
        if (!get_option('retail_pos_jwt_secret')) {
            update_option('retail_pos_jwt_secret', bin2hex(random_bytes(32)));
        }
        
        // 2. Categories Table
        $table_categories = $wpdb->prefix . 'pos_categories';
        $sql_categories = "CREATE TABLE $table_categories (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            slug varchar(100) NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_categories);

        // 3. Brands Table
        $table_brands = $wpdb->prefix . 'pos_brands';
        $sql_brands = "CREATE TABLE $table_brands (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            slug varchar(100) NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_brands);

        // 4. Products Table
        $table_products = $wpdb->prefix . 'pos_products';
        $sql_products = "CREATE TABLE $table_products (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            sku varchar(100) NOT NULL,
            barcode varchar(100) NOT NULL,
            product_name varchar(255) NOT NULL,
            category_id bigint(20) DEFAULT NULL,
            brand_id bigint(20) DEFAULT NULL,
            purchase_price decimal(10,2) NOT NULL DEFAULT 0.00,
            selling_price decimal(10,2) NOT NULL DEFAULT 0.00,
            gst_percentage decimal(5,2) NOT NULL DEFAULT 18.00,
            stock_quantity decimal(10,2) NOT NULL DEFAULT 0.00,
            minimum_stock decimal(10,2) NOT NULL DEFAULT 5.00,
            unit varchar(50) NOT NULL DEFAULT 'PCS',
            image varchar(255) DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY sku (sku),
            UNIQUE KEY barcode (barcode)
        ) $charset_collate;";
        dbDelta($sql_products);

        // 5. Customers Table
        $table_customers = $wpdb->prefix . 'pos_customers';
        $sql_customers = "CREATE TABLE $table_customers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            customer_code varchar(100) NOT NULL,
            name varchar(255) NOT NULL,
            mobile varchar(50) NOT NULL,
            email varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            gst_number varchar(50) DEFAULT NULL,
            loyalty_points int(11) NOT NULL DEFAULT 0,
            total_purchases decimal(12,2) NOT NULL DEFAULT 0.00,
            status varchar(50) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY customer_code (customer_code)
        ) $charset_collate;";
        dbDelta($sql_customers);

        // 6. Suppliers Table
        $table_suppliers = $wpdb->prefix . 'pos_suppliers';
        $sql_suppliers = "CREATE TABLE $table_suppliers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            supplier_name varchar(255) NOT NULL,
            mobile varchar(50) NOT NULL,
            email varchar(100) DEFAULT NULL,
            gst_number varchar(50) DEFAULT NULL,
            address text DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_suppliers);

        // 7. Sales/Invoices Table
        $table_sales = $wpdb->prefix . 'pos_sales';
        $sql_sales = "CREATE TABLE $table_sales (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_number varchar(100) NOT NULL,
            customer_id bigint(20) DEFAULT NULL,
            subtotal decimal(12,2) NOT NULL DEFAULT 0.00,
            discount decimal(12,2) NOT NULL DEFAULT 0.00,
            gst_amount decimal(12,2) NOT NULL DEFAULT 0.00,
            total_amount decimal(12,2) NOT NULL DEFAULT 0.00,
            payment_method varchar(50) NOT NULL DEFAULT 'Cash',
            invoice_date datetime NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'COMPLETED', -- COMPLETED, RETURNED
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY invoice_number (invoice_number)
        ) $charset_collate;";
        dbDelta($sql_sales);

        // 8. Sale Line Items Table
        $table_sale_items = $wpdb->prefix . 'pos_sale_items';
        $sql_sale_items = "CREATE TABLE $table_sale_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            sale_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            quantity decimal(10,2) NOT NULL DEFAULT 1.00,
            purchase_price decimal(10,2) NOT NULL DEFAULT 0.00,
            selling_price decimal(10,2) NOT NULL DEFAULT 0.00,
            gst_percentage decimal(5,2) NOT NULL DEFAULT 18.00,
            gst_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            discount decimal(10,2) NOT NULL DEFAULT 0.00,
            total decimal(12,2) NOT NULL DEFAULT 0.00,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_sale_items);

        // 9. Purchase Orders/Invoices Table
        $table_purchases = $wpdb->prefix . 'pos_purchases';
        $sql_purchases = "CREATE TABLE $table_purchases (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            po_number varchar(100) NOT NULL,
            supplier_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            quantity decimal(10,2) NOT NULL DEFAULT 0.00,
            purchase_price decimal(10,2) NOT NULL DEFAULT 0.00,
            gst_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            total_amount decimal(12,2) NOT NULL DEFAULT 0.00,
            purchase_date datetime NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'RECEIVED', -- ORDERED, RECEIVED, CANCELLED
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_purchases);

        // 10. Inventory Levels Table
        $table_inventory = $wpdb->prefix . 'pos_inventory';
        $sql_inventory = "CREATE TABLE $table_inventory (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            available_stock decimal(10,2) NOT NULL DEFAULT 0.00,
            reserved_stock decimal(10,2) NOT NULL DEFAULT 0.00,
            damaged_stock decimal(10,2) NOT NULL DEFAULT 0.00,
            minimum_stock decimal(10,2) NOT NULL DEFAULT 5.00,
            reorder_level decimal(10,2) NOT NULL DEFAULT 10.00,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY product_id (product_id)
        ) $charset_collate;";
        dbDelta($sql_inventory);

        // 11. Expenses Table
        $table_expenses = $wpdb->prefix . 'pos_expenses';
        $sql_expenses = "CREATE TABLE $table_expenses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            expense_type varchar(100) NOT NULL,
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            details text DEFAULT NULL,
            expense_date date NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_expenses);

        // 12. Loyalty Program Ledger Table
        $table_loyalty = $wpdb->prefix . 'pos_loyalty';
        $sql_loyalty = "CREATE TABLE $table_loyalty (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            customer_id bigint(20) NOT NULL,
            points int(11) NOT NULL,
            transaction_type varchar(50) NOT NULL, -- EARNED, REDEEMED
            sale_id bigint(20) DEFAULT NULL,
            remarks text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_loyalty);

        // 13. Stores Table
        $table_stores = $wpdb->prefix . 'pos_stores';
        $sql_stores = "CREATE TABLE $table_stores (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            store_name varchar(255) NOT NULL,
            store_code varchar(100) NOT NULL,
            address text DEFAULT NULL,
            manager varchar(255) DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY store_code (store_code)
        ) $charset_collate;";
        dbDelta($sql_stores);

        // 14. Documents Table
        $table_documents = $wpdb->prefix . 'pos_documents';
        $sql_documents = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            related_id bigint(20) NOT NULL,
            related_type varchar(50) NOT NULL,
            document_type varchar(100) NOT NULL,
            file_url varchar(255) NOT NULL,
            media_id bigint(20) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_documents);

        // 15. Activity Logs Table
        $table_logs = $wpdb->prefix . 'pos_activity_logs';
        $sql_logs = "CREATE TABLE $table_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            action varchar(100) NOT NULL,
            details text DEFAULT NULL,
            ip_address varchar(50) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_logs);

        // 16. Roles and Seed Data
        self::register_roles();
        self::seed_test_accounts();
        self::seed_sample_records();
    }
    
    /**
     * Register Custom POS Roles
     */
    private static function register_roles() {
        remove_role('pos_super_admin');
        remove_role('pos_store_manager');
        remove_role('pos_cashier');
        remove_role('pos_inventory_manager');
        
        $super_admin_caps = [
            'read' => true,
            'manage_pos' => true,
            'manage_users' => true,
            'manage_products' => true,
            'manage_inventory' => true,
            'manage_sales' => true,
            'manage_purchases' => true,
            'manage_suppliers' => true,
            'manage_customers' => true,
            'view_reports' => true,
            'view_dashboard' => true,
        ];
        
        $manager_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_products' => true,
            'manage_inventory' => true,
            'manage_sales' => true,
            'manage_purchases' => true,
            'view_reports' => true,
        ];
        
        $cashier_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_sales' => true,
            'manage_customers' => true,
        ];
        
        $inventory_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_inventory' => true,
            'manage_purchases' => true,
            'manage_suppliers' => true,
        ];
        
        add_role('pos_super_admin', 'POS Super Admin', $super_admin_caps);
        add_role('pos_store_manager', 'POS Store Manager', $manager_caps);
        add_role('pos_cashier', 'POS Cashier', $cashier_caps);
        add_role('pos_inventory_manager', 'POS Inventory Manager', $inventory_caps);

        // Ensure Administrator role also has the Super Admin caps
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($super_admin_caps as $cap => $grant) {
                $admin_role->add_cap($cap);
            }
        }
    }
    
    /**
     * Seed test accounts
     */
    private static function seed_test_accounts() {
        $super_admin_id = username_exists('possuperadmin');
        if ($super_admin_id) {
            wp_set_password('123456', $super_admin_id);
            $user = get_userdata($super_admin_id);
            if (!in_array('pos_super_admin', $user->roles)) {
                $user->set_role('pos_super_admin');
            }
            update_user_meta($super_admin_id, 'pos_user_status', 'APPROVED');
        } else {
            $user_id = wp_insert_user([
                'user_login' => 'possuperadmin',
                'user_email' => 'admin@pos.erp',
                'user_pass' => '123456',
                'display_name' => 'POS Super Admin',
                'first_name' => 'POS Super Admin',
                'role' => 'pos_super_admin'
            ]);
            if (!is_wp_error($user_id)) {
                update_user_meta($user_id, 'pos_user_status', 'APPROVED');
            }
        }

        self::create_test_user('pos_manager', 'manager@pos.erp', 'managerpass123', 'POS Store Manager', 'pos_store_manager');
        self::create_test_user('pos_cashier', 'cashier@pos.erp', 'cashierpass123', 'POS Cashier', 'pos_cashier');
        self::create_test_user('pos_inventory', 'inventory@pos.erp', 'inventorypass123', 'POS Inventory Manager', 'pos_inventory');
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
            update_user_meta($user_id, 'pos_user_status', 'APPROVED');
        }
    }
    
    /**
     * Seed sample records
     */
    private static function seed_sample_records() {
        global $wpdb;

        // 1. Seed Categories
        $table_categories = $wpdb->prefix . 'pos_categories';
        if ((int)$wpdb->get_var("SELECT COUNT(*) FROM $table_categories") === 0) {
            $wpdb->insert($table_categories, ['name' => 'Electronics', 'slug' => 'electronics', 'status' => 'ACTIVE']);
            $cat_elec = $wpdb->insert_id;
            
            $wpdb->insert($table_categories, ['name' => 'Grocery', 'slug' => 'grocery', 'status' => 'ACTIVE']);
            $cat_groc = $wpdb->insert_id;

            // 2. Seed Brands
            $table_brands = $wpdb->prefix . 'pos_brands';
            $wpdb->insert($table_brands, ['name' => 'Samsung', 'slug' => 'samsung', 'status' => 'ACTIVE']);
            $brand_sam = $wpdb->insert_id;
            
            $wpdb->insert($table_brands, ['name' => 'Nestle', 'slug' => 'nestle', 'status' => 'ACTIVE']);
            $brand_nes = $wpdb->insert_id;

            // 3. Seed Products
            $table_products = $wpdb->prefix . 'pos_products';
            $wpdb->insert($table_products, [
                'sku' => 'PROD-SAM-S26',
                'barcode' => '8806090123456',
                'product_name' => 'Samsung Galaxy S26 Ultra',
                'category_id' => $cat_elec,
                'brand_id' => $brand_sam,
                'purchase_price' => 85000.00,
                'selling_price' => 124999.00,
                'gst_percentage' => 18.00,
                'stock_quantity' => 15.00,
                'minimum_stock' => 3.00,
                'unit' => 'PCS',
                'status' => 'ACTIVE'
            ]);
            $prod1_id = $wpdb->insert_id;

            $wpdb->insert($table_products, [
                'sku' => 'PROD-NES-MAG',
                'barcode' => '8901058002314',
                'product_name' => 'Maggi 2-Minute Noodles 12-Pack',
                'category_id' => $cat_groc,
                'brand_id' => $brand_nes,
                'purchase_price' => 140.00,
                'selling_price' => 168.00,
                'gst_percentage' => 18.00,
                'stock_quantity' => 120.00,
                'minimum_stock' => 10.00,
                'unit' => 'PACKS',
                'status' => 'ACTIVE'
            ]);
            $prod2_id = $wpdb->insert_id;

            // 4. Seed Inventory
            $table_inventory = $wpdb->prefix . 'pos_inventory';
            $wpdb->insert($table_inventory, [
                'product_id' => $prod1_id,
                'available_stock' => 15.00,
                'reserved_stock' => 0.00,
                'damaged_stock' => 0.00,
                'minimum_stock' => 3.00,
                'reorder_level' => 5.00
            ]);
            $wpdb->insert($table_inventory, [
                'product_id' => $prod2_id,
                'available_stock' => 120.00,
                'reserved_stock' => 0.00,
                'damaged_stock' => 0.00,
                'minimum_stock' => 10.00,
                'reorder_level' => 20.00
            ]);

            // 5. Seed Customers
            $table_customers = $wpdb->prefix . 'pos_customers';
            $wpdb->insert($table_customers, [
                'customer_code' => 'CUST0001',
                'name' => 'Rakesh Patel',
                'mobile' => '9898012345',
                'email' => 'rakesh.patel@gmail.com',
                'address' => '302, Sunrise Towers, Ahmedabad',
                'gst_number' => '24AAAAA1111A1Z1',
                'loyalty_points' => 150,
                'total_purchases' => 125167.00,
                'status' => 'ACTIVE'
            ]);
            $cust1_id = $wpdb->insert_id;

            $wpdb->insert($table_customers, [
                'customer_code' => 'CUST0002',
                'name' => 'Kavita Sharma',
                'mobile' => '9879054321',
                'email' => 'kavita.sharma@yahoo.com',
                'address' => 'A-45, Shanti Kunj, Pune',
                'gst_number' => null,
                'loyalty_points' => 30,
                'total_purchases' => 168.00,
                'status' => 'ACTIVE'
            ]);
            $cust2_id = $wpdb->insert_id;

            // 6. Seed Suppliers
            $table_suppliers = $wpdb->prefix . 'pos_suppliers';
            $wpdb->insert($table_suppliers, [
                'supplier_name' => 'Apex Electronics Distributor',
                'mobile' => '9900887766',
                'email' => 'sales@apexelectronics.com',
                'gst_number' => '24BBBBB2222B2Z2',
                'address' => 'GIDC Industrial Estate, Vadodara, Gujarat',
                'status' => 'ACTIVE'
            ]);
            $sup1_id = $wpdb->insert_id;

            // 7. Seed Sales
            $table_sales = $wpdb->prefix . 'pos_sales';
            $wpdb->insert($table_sales, [
                'invoice_number' => 'INV-2026001',
                'customer_id' => $cust1_id,
                'subtotal' => 105931.36,
                'discount' => 0.00,
                'gst_amount' => 19067.64,
                'total_amount' => 124999.00,
                'payment_method' => 'UPI',
                'invoice_date' => current_time('mysql'),
                'status' => 'COMPLETED'
            ]);
            $sale1_id = $wpdb->insert_id;

            $table_sale_items = $wpdb->prefix . 'pos_sale_items';
            $wpdb->insert($table_sale_items, [
                'sale_id' => $sale1_id,
                'product_id' => $prod1_id,
                'quantity' => 1.00,
                'purchase_price' => 85000.00,
                'selling_price' => 124999.00,
                'gst_percentage' => 18.00,
                'gst_amount' => 19067.64,
                'discount' => 0.00,
                'total' => 124999.00
            ]);

            // Seed second sale
            $wpdb->insert($table_sales, [
                'invoice_number' => 'INV-2026002',
                'customer_id' => $cust2_id,
                'subtotal' => 142.37,
                'discount' => 0.00,
                'gst_amount' => 25.63,
                'total_amount' => 168.00,
                'payment_method' => 'Cash',
                'invoice_date' => current_time('mysql'),
                'status' => 'COMPLETED'
            ]);
            $sale2_id = $wpdb->insert_id;

            $wpdb->insert($table_sale_items, [
                'sale_id' => $sale2_id,
                'product_id' => $prod2_id,
                'quantity' => 1.00,
                'purchase_price' => 140.00,
                'selling_price' => 168.00,
                'gst_percentage' => 18.00,
                'gst_amount' => 25.63,
                'discount' => 0.00,
                'total' => 168.00
            ]);

            // 8. Seed Purchases
            $table_purchases = $wpdb->prefix . 'pos_purchases';
            $wpdb->insert($table_purchases, [
                'po_number' => 'PO-2026001',
                'supplier_id' => $sup1_id,
                'product_id' => $prod1_id,
                'quantity' => 10.00,
                'purchase_price' => 85000.00,
                'gst_amount' => 153000.00,
                'total_amount' => 1003000.00,
                'purchase_date' => current_time('mysql'),
                'status' => 'RECEIVED'
            ]);

            // 9. Seed Expenses
            $table_expenses = $wpdb->prefix . 'pos_expenses';
            $wpdb->insert($table_expenses, [
                'expense_type' => 'Rent',
                'amount' => 25000.00,
                'details' => 'Monthly store space rent payment',
                'expense_date' => date('Y-m-d')
            ]);
            $wpdb->insert($table_expenses, [
                'expense_type' => 'Electricity',
                'amount' => 8400.00,
                'details' => 'Store electricity bill',
                'expense_date' => date('Y-m-d')
            ]);

            // 10. Seed Loyalty Ledger
            $table_loyalty = $wpdb->prefix . 'pos_loyalty';
            $wpdb->insert($table_loyalty, [
                'customer_id' => $cust1_id,
                'points' => 150,
                'transaction_type' => 'EARNED',
                'sale_id' => $sale1_id,
                'remarks' => 'Points earned on INV-2026001'
            ]);
            $wpdb->insert($table_loyalty, [
                'customer_id' => $cust2_id,
                'points' => 30,
                'transaction_type' => 'EARNED',
                'sale_id' => $sale2_id,
                'remarks' => 'Points earned on INV-2026002'
            ]);

            // 11. Seed Store
            $table_stores = $wpdb->prefix . 'pos_stores';
            $wpdb->insert($table_stores, [
                'store_name' => 'Main POS Branch - Ahmedabad',
                'store_code' => 'MAIN-AHM',
                'address' => 'Shop 1-4, Galaxy Mall, Vastrapur, Ahmedabad',
                'manager' => 'Rajesh Kumar',
                'status' => 'ACTIVE'
            ]);
        }
    }
}
