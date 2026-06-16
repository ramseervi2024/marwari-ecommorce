<?php
namespace ManufacturingManagementApi\Database;

class Migrations {
    
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // 1. Raw Materials
        $table_raw_materials = $wpdb->prefix . 'mfg_raw_materials';
        $sql_raw_materials = "CREATE TABLE $table_raw_materials (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            material_code varchar(50) NOT NULL UNIQUE,
            material_name varchar(255) NOT NULL,
            category varchar(100) DEFAULT '',
            unit varchar(50) DEFAULT '',
            minimum_stock decimal(12,4) DEFAULT 0.0000,
            current_stock decimal(12,4) DEFAULT 0.0000,
            purchase_price decimal(12,2) DEFAULT 0.00,
            supplier_id bigint(20) DEFAULT NULL,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_raw_materials);

        // 2. Suppliers
        $table_suppliers = $wpdb->prefix . 'mfg_suppliers';
        $sql_suppliers = "CREATE TABLE $table_suppliers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            supplier_name varchar(255) NOT NULL,
            mobile varchar(50) DEFAULT '',
            email varchar(255) DEFAULT '',
            gst_number varchar(50) DEFAULT '',
            address text DEFAULT NULL,
            rating decimal(3,2) DEFAULT 5.00,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_suppliers);

        // 3. Purchases
        $table_purchases = $wpdb->prefix . 'mfg_purchases';
        $sql_purchases = "CREATE TABLE $table_purchases (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            po_number varchar(50) NOT NULL UNIQUE,
            supplier_id bigint(20) NOT NULL,
            material_id bigint(20) NOT NULL,
            quantity decimal(12,4) NOT NULL,
            rate decimal(12,2) NOT NULL,
            gst_amount decimal(12,2) DEFAULT 0.00,
            total_amount decimal(12,2) NOT NULL,
            purchase_date datetime NOT NULL,
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_purchases);

        // 4. BOM (Bill of Materials)
        $table_bom = $wpdb->prefix . 'mfg_bom';
        $sql_bom = "CREATE TABLE $table_bom (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            material_id bigint(20) NOT NULL,
            required_quantity decimal(12,4) NOT NULL,
            unit varchar(50) DEFAULT '',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_bom);

        // 5. Production Plans
        $table_production_plans = $wpdb->prefix . 'mfg_production_plans';
        $sql_production_plans = "CREATE TABLE $table_production_plans (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            plan_number varchar(50) NOT NULL UNIQUE,
            product_id bigint(20) NOT NULL,
            planned_quantity decimal(12,4) NOT NULL,
            planned_start_date datetime NOT NULL,
            planned_end_date datetime NOT NULL,
            priority varchar(50) DEFAULT 'MEDIUM',
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_production_plans);

        // 6. Work Orders
        $table_work_orders = $wpdb->prefix . 'mfg_work_orders';
        $sql_work_orders = "CREATE TABLE $table_work_orders (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            work_order_number varchar(50) NOT NULL UNIQUE,
            production_plan_id bigint(20) DEFAULT NULL,
            product_id bigint(20) NOT NULL,
            quantity decimal(12,4) NOT NULL,
            assigned_to bigint(20) DEFAULT NULL,
            start_date datetime DEFAULT NULL,
            end_date datetime DEFAULT NULL,
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_work_orders);

        // 7. Job Work (Outsourced)
        $table_job_work = $wpdb->prefix . 'mfg_job_work';
        $sql_job_work = "CREATE TABLE $table_job_work (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            job_work_number varchar(50) NOT NULL UNIQUE,
            vendor_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            quantity decimal(12,4) NOT NULL,
            job_cost decimal(12,2) NOT NULL,
            dispatch_date datetime DEFAULT NULL,
            expected_return_date datetime DEFAULT NULL,
            actual_return_date datetime DEFAULT NULL,
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_job_work);

        // 8. Production Actual logs
        $table_production = $wpdb->prefix . 'mfg_production';
        $sql_production = "CREATE TABLE $table_production (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            work_order_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            quantity_produced decimal(12,4) NOT NULL,
            production_date datetime NOT NULL,
            production_cost decimal(12,2) DEFAULT 0.00,
            machine_id bigint(20) DEFAULT NULL,
            operator varchar(255) DEFAULT '',
            status varchar(50) DEFAULT 'COMPLETED',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_production);

        // 9. Finished Goods
        $table_finished_goods = $wpdb->prefix . 'mfg_finished_goods';
        $sql_finished_goods = "CREATE TABLE $table_finished_goods (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_code varchar(50) NOT NULL UNIQUE,
            product_name varchar(255) NOT NULL,
            quantity decimal(12,4) DEFAULT 0.0000,
            warehouse varchar(255) DEFAULT '',
            selling_price decimal(12,2) DEFAULT 0.00,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_finished_goods);

        // 10. Inventory transaction logs
        $table_inventory = $wpdb->prefix . 'mfg_inventory';
        $sql_inventory = "CREATE TABLE $table_inventory (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            item_type varchar(50) NOT NULL, /* RAW or FINISHED */
            item_id bigint(20) NOT NULL,
            movement_type varchar(50) NOT NULL, /* IN, OUT, ADJUSTMENT */
            quantity decimal(12,4) NOT NULL,
            reference varchar(255) DEFAULT '',
            created_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_inventory);

        // 11. Quality checks
        $table_quality = $wpdb->prefix . 'mfg_quality';
        $sql_quality = "CREATE TABLE $table_quality (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            inspection_number varchar(50) NOT NULL UNIQUE,
            work_order_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            inspection_date datetime NOT NULL,
            approved_quantity decimal(12,4) NOT NULL,
            rejected_quantity decimal(12,4) NOT NULL,
            remarks text DEFAULT NULL,
            status varchar(50) DEFAULT 'PASSED',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_quality);

        // 12. Dispatch logistics
        $table_dispatch = $wpdb->prefix . 'mfg_dispatch';
        $sql_dispatch = "CREATE TABLE $table_dispatch (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            dispatch_number varchar(50) NOT NULL UNIQUE,
            customer_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            quantity decimal(12,4) NOT NULL,
            vehicle_number varchar(50) DEFAULT '',
            driver_name varchar(255) DEFAULT '',
            dispatch_date datetime NOT NULL,
            delivery_date datetime DEFAULT NULL,
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_dispatch);

        // 13. Warehouses
        $table_warehouses = $wpdb->prefix . 'mfg_warehouses';
        $sql_warehouses = "CREATE TABLE $table_warehouses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            warehouse_name varchar(255) NOT NULL,
            location varchar(255) DEFAULT '',
            manager varchar(255) DEFAULT '',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_warehouses);

        // 14. Machines
        $table_machines = $wpdb->prefix . 'mfg_machines';
        $sql_machines = "CREATE TABLE $table_machines (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            machine_code varchar(50) NOT NULL UNIQUE,
            machine_name varchar(255) NOT NULL,
            capacity varchar(255) DEFAULT '',
            maintenance_due datetime DEFAULT NULL,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_machines);

        // 15. Activity logs
        $table_activity_logs = $wpdb->prefix . 'mfg_activity_logs';
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
        remove_role('mfg_super_admin');
        remove_role('mfg_production_manager');
        remove_role('mfg_purchase_manager');
        remove_role('mfg_store_manager');
        remove_role('mfg_quality_inspector');
        remove_role('mfg_dispatch_manager');
        
        add_role('mfg_super_admin', 'Mfg Super Admin', [
            'read' => true,
            'manage_users' => true,
            'manage_mfg_setup' => true,
            'manage_production' => true,
            'manage_purchases' => true,
            'manage_store' => true,
            'manage_quality' => true,
            'manage_dispatch' => true,
        ]);
        
        add_role('mfg_production_manager', 'Mfg Production Manager', [
            'read' => true,
            'manage_production' => true,
            'manage_store' => true,
            'manage_quality' => true,
        ]);
        
        add_role('mfg_purchase_manager', 'Mfg Purchase Manager', [
            'read' => true,
            'manage_purchases' => true,
        ]);
        
        add_role('mfg_store_manager', 'Mfg Store Manager', [
            'read' => true,
            'manage_store' => true,
        ]);
        
        add_role('mfg_quality_inspector', 'Mfg Quality Inspector', [
            'read' => true,
            'manage_quality' => true,
        ]);
        
        add_role('mfg_dispatch_manager', 'Mfg Dispatch Manager', [
            'read' => true,
            'manage_dispatch' => true,
        ]);
        
        // Bind capabilities to administrator
        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap('manage_users');
            $admin->add_cap('manage_mfg_setup');
            $admin->add_cap('manage_production');
            $admin->add_cap('manage_purchases');
            $admin->add_cap('manage_store');
            $admin->add_cap('manage_quality');
            $admin->add_cap('manage_dispatch');
        }
    }
    
    private static function seedDatabase() {
        global $wpdb;
        $now = current_time('mysql');
        
        // 1. Seed standard mock user operators
        $users_to_seed = [
            [
                'username' => 'mfgsuperadmin',
                'password' => '123456',
                'email' => 'mfgadmin@mfg.erp',
                'name' => 'Mfg Super Admin',
                'role' => 'mfg_super_admin'
            ],
            [
                'username' => 'mfg_production',
                'password' => 'productionpass123',
                'email' => 'production@mfg.erp',
                'name' => 'Production Manager',
                'role' => 'mfg_production_manager'
            ],
            [
                'username' => 'mfg_purchase',
                'password' => 'purchasepass123',
                'email' => 'purchase@mfg.erp',
                'name' => 'Purchase Manager',
                'role' => 'mfg_purchase_manager'
            ],
            [
                'username' => 'mfg_store',
                'password' => 'storepass123',
                'email' => 'store@mfg.erp',
                'name' => 'Store Manager',
                'role' => 'mfg_store_manager'
            ],
            [
                'username' => 'mfg_quality',
                'password' => 'qualitypass123',
                'email' => 'quality@mfg.erp',
                'name' => 'Quality Inspector',
                'role' => 'mfg_quality_inspector'
            ],
            [
                'username' => 'mfg_dispatch',
                'password' => 'dispatchpass123',
                'email' => 'dispatch@mfg.erp',
                'name' => 'Dispatch Manager',
                'role' => 'mfg_dispatch_manager'
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
                    update_user_meta($user_id, 'restaurant_user_status', 'APPROVED'); // Support across erps
                    update_user_meta($user_id, 'mfg_user_status', 'APPROVED');
                }
            }
        }
        
        // 2. Seed mock B2B suppliers
        $table_suppliers = $wpdb->prefix . 'mfg_suppliers';
        $supplier_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_suppliers");
        if (intval($supplier_count) === 0) {
            $suppliers = [
                [
                    'supplier_name' => 'Jindal Steel & Power',
                    'mobile' => '+919876543210',
                    'email' => 'sales@jindalsteel.com',
                    'gst_number' => '07AAAAA1111A1Z1',
                    'address' => 'Plot No. 12, Sector 3, IMT Manesar, Gurugram, Haryana',
                    'rating' => 4.80,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'supplier_name' => 'Supreme Petrochem Ltd',
                    'mobile' => '+919876543211',
                    'email' => 'info@supremepetro.com',
                    'gst_number' => '27BBBBB2222B2Z2',
                    'address' => 'Solitaire Corporate Park, Andheri East, Mumbai, Maharashtra',
                    'rating' => 4.50,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'supplier_name' => 'Apex Packaging Industries',
                    'mobile' => '+919876543212',
                    'email' => 'orders@apexpackaging.in',
                    'gst_number' => '08CCCCC3333C3Z3',
                    'address' => 'Industrial Area Phase 2, Jaipur, Rajasthan',
                    'rating' => 4.20,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($suppliers as $supplier) {
                $wpdb->insert($table_suppliers, $supplier);
            }
        }
        
        // 3. Seed mock raw materials
        $table_raw_materials = $wpdb->prefix . 'mfg_raw_materials';
        $material_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_raw_materials");
        if (intval($material_count) === 0) {
            $materials = [
                [
                    'material_code' => 'RAW-STL-001',
                    'material_name' => 'Stainless Steel Sheet (1.2mm)',
                    'category' => 'Metals',
                    'unit' => 'KG',
                    'minimum_stock' => 100.0000,
                    'current_stock' => 350.0000,
                    'purchase_price' => 120.00,
                    'supplier_id' => 1,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'material_code' => 'RAW-PLT-002',
                    'material_name' => 'Polypropylene Granules',
                    'category' => 'Polymers',
                    'unit' => 'KG',
                    'minimum_stock' => 200.0000,
                    'current_stock' => 800.0000,
                    'purchase_price' => 85.00,
                    'supplier_id' => 2,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'material_code' => 'RAW-BOX-003',
                    'material_name' => 'Corrugated Box (Large)',
                    'category' => 'Packaging',
                    'unit' => 'PCS',
                    'minimum_stock' => 50.0000,
                    'current_stock' => 150.0000,
                    'purchase_price' => 15.00,
                    'supplier_id' => 3,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($materials as $material) {
                $wpdb->insert($table_raw_materials, $material);
            }
        }
        
        // 4. Seed mock finished goods catalog
        $table_finished_goods = $wpdb->prefix . 'mfg_finished_goods';
        $product_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_finished_goods");
        if (intval($product_count) === 0) {
            $products = [
                [
                    'product_code' => 'FG-CAB-101',
                    'product_name' => 'Industrial Steel Cabinet',
                    'quantity' => 15.0000,
                    'warehouse' => 'Main Warehouse Alpha',
                    'selling_price' => 4500.00,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'product_code' => 'FG-TRAY-202',
                    'product_name' => 'Heavy Duty Plastic Organizer Tray',
                    'quantity' => 45.0000,
                    'warehouse' => 'Storage Warehouse Beta',
                    'selling_price' => 320.00,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($products as $prod) {
                $wpdb->insert($table_finished_goods, $prod);
            }
        }
        
        // 5. Seed Bill of Materials (BOM)
        $table_bom = $wpdb->prefix . 'mfg_bom';
        $bom_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_bom");
        if (intval($bom_count) === 0) {
            $boms = [
                // Cabinet BOM: 8 KG steel + 1 Corrugated Box
                [
                    'product_id' => 1,
                    'material_id' => 1,
                    'required_quantity' => 8.5000,
                    'unit' => 'KG',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'product_id' => 1,
                    'material_id' => 3,
                    'required_quantity' => 1.0000,
                    'unit' => 'PCS',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                // Tray BOM: 0.5 KG polymer + 0.1 Corrugated Box (packed in bulk)
                [
                    'product_id' => 2,
                    'material_id' => 2,
                    'required_quantity' => 0.4500,
                    'unit' => 'KG',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($boms as $b) {
                $wpdb->insert($table_bom, $b);
            }
        }
        
        // 6. Seed mock warehouses
        $table_warehouses = $wpdb->prefix . 'mfg_warehouses';
        $wh_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_warehouses");
        if (intval($wh_count) === 0) {
            $warehouses = [
                [
                    'warehouse_name' => 'Main Warehouse Alpha',
                    'location' => 'Block A, Plot 12, IMT Manesar',
                    'manager' => 'Rajesh Sharma',
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'warehouse_name' => 'Storage Warehouse Beta',
                    'location' => 'Block B, Plot 13, IMT Manesar',
                    'manager' => 'Amit Verma',
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($warehouses as $wh) {
                $wpdb->insert($table_warehouses, $wh);
            }
        }
        
        // 7. Seed mock machinery
        $table_machines = $wpdb->prefix . 'mfg_machines';
        $machine_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_machines");
        if (intval($machine_count) === 0) {
            $machines = [
                [
                    'machine_code' => 'MAC-CNC-01',
                    'machine_name' => 'CNC Laser Cutting Machine (1500W)',
                    'capacity' => '50 cuts/minute',
                    'maintenance_due' => date('Y-m-d H:i:s', strtotime('+30 days')),
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'machine_code' => 'MAC-INJ-02',
                    'machine_name' => 'Plastic Injection Molding Machine (250T)',
                    'capacity' => '120 cycles/hour',
                    'maintenance_due' => date('Y-m-d H:i:s', strtotime('+15 days')),
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'machine_code' => 'MAC-HYD-03',
                    'machine_name' => 'Hydraulic Press Bending Machine (100T)',
                    'capacity' => '30 bends/minute',
                    'maintenance_due' => date('Y-m-d H:i:s', strtotime('+45 days')),
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($machines as $mac) {
                $wpdb->insert($table_machines, $mac);
            }
        }
    }
}
