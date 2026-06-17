<?php
namespace JewelleryManagementApi\Database;

class Migrations {
    
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // 1. Finished Inventory
        $table_inventory = $wpdb->prefix . 'jewel_inventory';
        $sql_inventory = "CREATE TABLE $table_inventory (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            barcode varchar(50) NOT NULL UNIQUE,
            sku varchar(50) DEFAULT '',
            product_name varchar(255) NOT NULL,
            category varchar(100) DEFAULT '',
            metal_type varchar(50) DEFAULT 'Gold', /* Gold, Silver, Platinum, Diamond */
            purity varchar(50) DEFAULT '', /* 24K, 22K, 18K, 925, etc */
            gross_weight decimal(10,3) DEFAULT 0.000,
            stone_weight decimal(10,3) DEFAULT 0.000,
            net_weight decimal(10,3) DEFAULT 0.000,
            making_charges decimal(12,2) DEFAULT 0.00,
            purchase_price decimal(12,2) DEFAULT 0.00,
            selling_price decimal(12,2) DEFAULT 0.00,
            hallmark_number varchar(100) DEFAULT '',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_inventory);

        // 2. Gold/Silver Bullion Raw Stock
        $table_metal_stock = $wpdb->prefix . 'jewel_metal_stock';
        $sql_metal_stock = "CREATE TABLE $table_metal_stock (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            metal_type varchar(50) NOT NULL, /* Gold, Silver, Platinum */
            purity varchar(50) NOT NULL,
            weight decimal(12,3) NOT NULL, /* grams */
            rate_per_gram decimal(12,2) DEFAULT 0.00,
            total_value decimal(12,2) DEFAULT 0.00,
            location varchar(255) DEFAULT '',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_metal_stock);

        // 3. Customers
        $table_customers = $wpdb->prefix . 'jewel_customers';
        $sql_customers = "CREATE TABLE $table_customers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            customer_code varchar(50) NOT NULL UNIQUE,
            name varchar(255) NOT NULL,
            mobile varchar(50) DEFAULT '',
            email varchar(255) DEFAULT '',
            address text DEFAULT NULL,
            aadhaar_number varchar(50) DEFAULT '',
            pan_number varchar(50) DEFAULT '',
            loyalty_points int(11) DEFAULT 0,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_customers);

        // 4. Billing & GST Invoices
        $table_billing = $wpdb->prefix . 'jewel_billing';
        $sql_billing = "CREATE TABLE $table_billing (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_number varchar(50) NOT NULL UNIQUE,
            customer_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            gross_weight decimal(10,3) NOT NULL,
            net_weight decimal(10,3) NOT NULL,
            gold_rate decimal(12,2) DEFAULT 0.00,
            silver_rate decimal(12,2) DEFAULT 0.00,
            making_charges decimal(12,2) DEFAULT 0.00,
            stone_charges decimal(12,2) DEFAULT 0.00,
            gst_amount decimal(12,2) DEFAULT 0.00,
            discount decimal(12,2) DEFAULT 0.00,
            total_amount decimal(12,2) NOT NULL,
            payment_method varchar(50) DEFAULT 'CASH',
            invoice_date datetime NOT NULL,
            status varchar(50) DEFAULT 'PAID',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_billing);

        // 5. Karigars
        $table_karigars = $wpdb->prefix . 'jewel_karigars';
        $sql_karigars = "CREATE TABLE $table_karigars (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            karigar_code varchar(50) NOT NULL UNIQUE,
            name varchar(255) NOT NULL,
            mobile varchar(50) DEFAULT '',
            specialization varchar(100) DEFAULT '', /* Gold Work, Silver Work, Diamond Setting, Polishing, Repair */
            daily_rate decimal(12,2) DEFAULT 0.00,
            per_gram_rate decimal(12,2) DEFAULT 0.00,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_karigars);

        // 6. Karigar Job Work Allocations
        $table_job_work = $wpdb->prefix . 'jewel_job_work';
        $sql_job_work = "CREATE TABLE $table_job_work (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            job_number varchar(50) NOT NULL UNIQUE,
            karigar_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            metal_weight decimal(10,3) NOT NULL,
            expected_completion datetime DEFAULT NULL,
            actual_completion datetime DEFAULT NULL,
            labor_cost decimal(12,2) DEFAULT 0.00,
            status varchar(50) DEFAULT 'Assigned', /* Assigned, In Progress, Completed, Delivered */
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_job_work);

        // 7. Repair Orders
        $table_repairs = $wpdb->prefix . 'jewel_repairs';
        $sql_repairs = "CREATE TABLE $table_repairs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            repair_number varchar(50) NOT NULL UNIQUE,
            customer_id bigint(20) NOT NULL,
            product_description varchar(255) NOT NULL,
            issue_description text DEFAULT NULL,
            received_weight decimal(10,3) DEFAULT 0.000,
            repair_cost decimal(12,2) DEFAULT 0.00,
            expected_delivery datetime DEFAULT NULL,
            status varchar(50) DEFAULT 'Received', /* Received, Under Repair, Ready, Delivered */
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_repairs);

        // 8. Custom Bookings Orders
        $table_custom_orders = $wpdb->prefix . 'jewel_custom_orders';
        $sql_custom_orders = "CREATE TABLE $table_custom_orders (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_number varchar(50) NOT NULL UNIQUE,
            customer_id bigint(20) NOT NULL,
            design_reference varchar(255) DEFAULT '', /* Media URL */
            metal_type varchar(50) DEFAULT 'Gold',
            purity varchar(50) DEFAULT '',
            weight_estimate decimal(10,3) DEFAULT 0.000,
            advance_amount decimal(12,2) DEFAULT 0.00,
            delivery_date datetime DEFAULT NULL,
            status varchar(50) DEFAULT 'Pending',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_custom_orders);

        // 9. Buyback & Exchange Ledger
        $table_buyback = $wpdb->prefix . 'jewel_buyback';
        $sql_buyback = "CREATE TABLE $table_buyback (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            transaction_number varchar(50) NOT NULL UNIQUE,
            customer_id bigint(20) NOT NULL,
            metal_type varchar(50) NOT NULL,
            purity varchar(50) NOT NULL,
            weight decimal(10,3) NOT NULL,
            rate_per_gram decimal(12,2) NOT NULL,
            payout_amount decimal(12,2) NOT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_buyback);

        // 10. Diamonds inventory
        $table_diamonds = $wpdb->prefix . 'jewel_diamonds';
        $sql_diamonds = "CREATE TABLE $table_diamonds (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            diamond_code varchar(50) NOT NULL UNIQUE,
            shape varchar(50) DEFAULT '',
            carat decimal(6,3) DEFAULT 0.000,
            clarity varchar(50) DEFAULT '',
            color varchar(50) DEFAULT '',
            certificate_number varchar(100) DEFAULT '',
            purchase_price decimal(12,2) DEFAULT 0.00,
            selling_price decimal(12,2) DEFAULT 0.00,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_diamonds);

        // 11. Store expenses
        $table_expenses = $wpdb->prefix . 'jewel_expenses';
        $sql_expenses = "CREATE TABLE $table_expenses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            expense_type varchar(100) NOT NULL, /* Rent, Salary, Electricity, Security, Marketing, Miscellaneous */
            amount decimal(12,2) NOT NULL,
            description text DEFAULT NULL,
            payment_date datetime NOT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_expenses);

        // 12. Customer Loyalty details
        $table_loyalty = $wpdb->prefix . 'jewel_loyalty';
        $sql_loyalty = "CREATE TABLE $table_loyalty (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            customer_id bigint(20) NOT NULL,
            points_earned int(11) DEFAULT 0,
            points_redeemed int(11) DEFAULT 0,
            membership_level varchar(50) DEFAULT 'Silver', /* Silver, Gold, Platinum */
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_loyalty);

        // 13. Inventory Audits Variances
        $table_inventory_audit = $wpdb->prefix . 'jewel_inventory_audit';
        $sql_inventory_audit = "CREATE TABLE $table_inventory_audit (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            audit_number varchar(50) NOT NULL,
            auditor_name varchar(255) NOT NULL,
            item_id bigint(20) NOT NULL,
            physical_qty decimal(12,3) NOT NULL,
            system_qty decimal(12,3) NOT NULL,
            variance decimal(12,3) NOT NULL,
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_inventory_audit);

        // 14. Activity logs
        $table_activity_logs = $wpdb->prefix . 'jewel_activity_logs';
        $sql_activity_logs = "CREATE TABLE $table_activity_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            action_type varchar(100) NOT NULL,
            description text NOT NULL,
            ip_address varchar(100) DEFAULT '',
            created_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_activity_logs);
        
        // Setup custom operator roles
        self::registerRoles();
        
        // Seed initial parameters
        self::seedDatabase();
    }
    
    private static function registerRoles() {
        // Remove existing if any to avoid dirty configurations
        remove_role('jewel_super_admin');
        remove_role('jewel_store_manager');
        remove_role('jewel_sales_executive');
        remove_role('jewel_karigar_supervisor');
        remove_role('jewel_accountant');
        
        add_role('jewel_super_admin', 'Jewel Super Admin', [
            'read' => true,
            'manage_users' => true,
            'manage_jewel_setup' => true,
            'manage_jewel_inventory' => true,
            'manage_jewel_billing' => true,
            'manage_jewel_karigars' => true,
            'manage_jewel_orders' => true,
            'manage_jewel_reports' => true,
        ]);
        
        add_role('jewel_store_manager', 'Jewel Store Manager', [
            'read' => true,
            'manage_jewel_inventory' => true,
            'manage_jewel_billing' => true,
            'manage_jewel_karigars' => true,
            'manage_jewel_reports' => true,
        ]);
        
        add_role('jewel_sales_executive', 'Jewel Sales Executive', [
            'read' => true,
            'manage_jewel_billing' => true,
            'manage_jewel_orders' => true,
        ]);
        
        add_role('jewel_karigar_supervisor', 'Jewel Karigar Supervisor', [
            'read' => true,
            'manage_jewel_karigars' => true,
            'manage_jewel_orders' => true,
        ]);
        
        add_role('jewel_accountant', 'Jewel Accountant', [
            'read' => true,
            'manage_jewel_billing' => true,
            'manage_jewel_reports' => true,
        ]);
        
        // Bind capabilities to administrator
        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap('manage_users');
            $admin->add_cap('manage_jewel_setup');
            $admin->add_cap('manage_jewel_inventory');
            $admin->add_cap('manage_jewel_billing');
            $admin->add_cap('manage_jewel_karigars');
            $admin->add_cap('manage_jewel_orders');
            $admin->add_cap('manage_jewel_reports');
        }
    }
    
    private static function seedDatabase() {
        global $wpdb;
        $now = current_time('mysql');
        
        // 1. Seed standard mock user operators
        $users_to_seed = [
            [
                'username' => 'jewelsuperadmin',
                'password' => '123456',
                'email' => 'jeweladmin@jewel.erp',
                'name' => 'Jewel Super Admin',
                'role' => 'jewel_super_admin'
            ],
            [
                'username' => 'jwl_manager',
                'password' => 'managerpass123',
                'email' => 'jwlmanager@jewel.erp',
                'name' => 'Jewel Store Manager',
                'role' => 'jewel_store_manager'
            ],
            [
                'username' => 'jwl_sales',
                'password' => 'salespass123',
                'email' => 'jwlsales@jewel.erp',
                'name' => 'Jewel Sales Executive',
                'role' => 'jewel_sales_executive'
            ],
            [
                'username' => 'jwl_supervisor',
                'password' => 'supervisorpass123',
                'email' => 'jwlsupervisor@jewel.erp',
                'name' => 'Jewel Karigar Supervisor',
                'role' => 'jewel_karigar_supervisor'
            ],
            [
                'username' => 'jwl_accountant',
                'password' => 'accountpass123',
                'email' => 'jwlaccountant@jewel.erp',
                'name' => 'Jewel Accountant',
                'role' => 'jewel_accountant'
            ]
        ];
        
        foreach ($users_to_seed as $seed) {
            $user_id = username_exists($seed['username']);
            if (!$user_id && email_exists($seed['email']) === false) {
                $user_id = wp_create_user($seed['username'], $seed['password'], $seed['email']);
                if (!is_wp_error($user_id)) {
                    wp_update_user([
                        'ID' => $user_id,
                        'display_name' => $seed['name']
                    ]);
                    $user = new \WP_User($user_id);
                    $user->set_role($seed['role']);
                    update_user_meta($user_id, 'jewel_user_status', 'APPROVED');
                }
            }
        }
        
        // 2. Seed Gold/Silver metal stocks
        $table_metal_stock = $wpdb->prefix . 'jewel_metal_stock';
        $metal_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_metal_stock");
        if (intval($metal_count) === 0) {
            $metals = [
                [
                    'metal_type' => 'Gold',
                    'purity' => '22K',
                    'weight' => 2500.000,
                    'rate_per_gram' => 6250.00,
                    'total_value' => 15625000.00,
                    'location' => 'Main Safe Alpha',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'metal_type' => 'Silver',
                    'purity' => '925',
                    'weight' => 15000.000,
                    'rate_per_gram' => 75.00,
                    'total_value' => 1125000.00,
                    'location' => 'Safe Beta',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($metals as $m) {
                $wpdb->insert($table_metal_stock, $m);
            }
        }

        // 3. Seed mock karigars
        $table_karigars = $wpdb->prefix . 'jewel_karigars';
        $karigar_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_karigars");
        if (intval($karigar_count) === 0) {
            $karigars = [
                [
                    'karigar_code' => 'KARI-001',
                    'name' => 'Mahesh Karigar',
                    'mobile' => '+919876543240',
                    'specialization' => 'Gold Work',
                    'daily_rate' => 1500.00,
                    'per_gram_rate' => 120.00,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'karigar_code' => 'KARI-002',
                    'name' => 'Rajesh Karigar',
                    'mobile' => '+919876543241',
                    'specialization' => 'Diamond Setting',
                    'daily_rate' => 2000.00,
                    'per_gram_rate' => 250.00,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($karigars as $k) {
                $wpdb->insert($table_karigars, $k);
            }
        }
        
        // 4. Seed finished inventory ornaments
        $table_inventory = $wpdb->prefix . 'jewel_inventory';
        $inv_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_inventory");
        if (intval($inv_count) === 0) {
            $items = [
                [
                    'barcode' => 'JWL-2026-001',
                    'sku' => 'SKU-GLD-NCKL-01',
                    'product_name' => 'Royal Bridal Necklace 22K',
                    'category' => 'Necklace',
                    'metal_type' => 'Gold',
                    'purity' => '22K',
                    'gross_weight' => 45.500,
                    'stone_weight' => 2.300,
                    'net_weight' => 43.200,
                    'making_charges' => 450.00, // per gram net weight
                    'purchase_price' => 260000.00,
                    'selling_price' => 310000.00,
                    'hallmark_number' => 'HM-2026-999',
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'barcode' => 'JWL-2026-002',
                    'sku' => 'SKU-SLV-RING-02',
                    'product_name' => 'Vintage Filigree Silver Ring',
                    'category' => 'Ring',
                    'metal_type' => 'Silver',
                    'purity' => '925',
                    'gross_weight' => 8.200,
                    'stone_weight' => 0.000,
                    'net_weight' => 8.200,
                    'making_charges' => 50.00,
                    'purchase_price' => 600.00,
                    'selling_price' => 1200.00,
                    'hallmark_number' => 'HM-2026-888',
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($items as $item) {
                $wpdb->insert($table_inventory, $item);
            }
        }
    }
}
