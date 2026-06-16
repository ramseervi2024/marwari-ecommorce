<?php
namespace GarmentManagementApi\Database;

class Migrations {
    
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // 1. Orders
        $table_orders = $wpdb->prefix . 'garment_orders';
        $sql_orders = "CREATE TABLE $table_orders (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_number varchar(50) NOT NULL UNIQUE,
            customer_name varchar(255) NOT NULL,
            product_name varchar(255) NOT NULL,
            style_code varchar(50) DEFAULT '',
            quantity decimal(12,4) NOT NULL,
            unit_price decimal(12,2) DEFAULT 0.00,
            delivery_date datetime NOT NULL,
            status varchar(50) DEFAULT 'Pending',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_orders);

        // 2. Fabrics
        $table_fabrics = $wpdb->prefix . 'garment_fabrics';
        $sql_fabrics = "CREATE TABLE $table_fabrics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            fabric_code varchar(50) NOT NULL UNIQUE,
            fabric_name varchar(255) NOT NULL,
            fabric_type varchar(100) DEFAULT '',
            color varchar(50) DEFAULT '',
            gsm int(11) DEFAULT 0,
            width decimal(8,2) DEFAULT 0.00,
            available_meters decimal(12,4) DEFAULT 0.0000,
            cost_per_meter decimal(12,2) DEFAULT 0.00,
            supplier_id bigint(20) DEFAULT NULL,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_fabrics);

        // 3. Accessories
        $table_accessories = $wpdb->prefix . 'garment_accessories';
        $sql_accessories = "CREATE TABLE $table_accessories (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            accessory_name varchar(255) NOT NULL,
            category varchar(100) NOT NULL, /* buttons, zippers, labels, threads, packaging, etc. */
            available_quantity decimal(12,4) DEFAULT 0.0000,
            unit varchar(50) DEFAULT '',
            cost_per_unit decimal(12,2) DEFAULT 0.00,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_accessories);

        // 4. Suppliers
        $table_suppliers = $wpdb->prefix . 'garment_suppliers';
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

        // 5. Purchases
        $table_purchases = $wpdb->prefix . 'garment_purchases';
        $sql_purchases = "CREATE TABLE $table_purchases (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            po_number varchar(50) NOT NULL UNIQUE,
            supplier_id bigint(20) NOT NULL,
            item_type varchar(50) NOT NULL, /* FABRIC or ACCESSORY */
            item_id bigint(20) NOT NULL,
            quantity decimal(12,4) NOT NULL,
            rate decimal(12,2) NOT NULL,
            total_amount decimal(12,2) NOT NULL,
            purchase_date datetime NOT NULL,
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_purchases);

        // 6. BOM (Bill of Materials)
        $table_bom = $wpdb->prefix . 'garment_bom';
        $sql_bom = "CREATE TABLE $table_bom (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id varchar(255) NOT NULL, /* references product_name or style_code */
            fabric_id bigint(20) NOT NULL,
            fabric_requirement decimal(12,4) NOT NULL, /* meters per piece */
            accessories_requirement text DEFAULT NULL, /* JSON formatted requirements */
            estimated_cost decimal(12,2) DEFAULT 0.00,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_bom);

        // 7. Production Plans
        $table_production_plans = $wpdb->prefix . 'garment_production_plans';
        $sql_production_plans = "CREATE TABLE $table_production_plans (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            plan_number varchar(50) NOT NULL UNIQUE,
            order_id bigint(20) NOT NULL,
            planned_quantity decimal(12,4) NOT NULL,
            start_date datetime NOT NULL,
            end_date datetime NOT NULL,
            priority varchar(50) DEFAULT 'MEDIUM',
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_production_plans);

        // 8. Cutting Management
        $table_cutting = $wpdb->prefix . 'garment_cutting';
        $sql_cutting = "CREATE TABLE $table_cutting (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            cutting_number varchar(50) NOT NULL UNIQUE,
            order_id bigint(20) NOT NULL,
            fabric_id bigint(20) NOT NULL,
            layers int(11) DEFAULT 1,
            planned_pieces decimal(12,4) NOT NULL,
            actual_pieces decimal(12,4) DEFAULT 0.0000,
            wastage_meters decimal(12,4) DEFAULT 0.0000,
            cutting_date datetime NOT NULL,
            operator_name varchar(255) DEFAULT '',
            status varchar(50) DEFAULT 'PENDING', /* PENDING, COMPLETED, CANCELLED */
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_cutting);

        // 9. Stitching Management
        $table_stitching = $wpdb->prefix . 'garment_stitching';
        $sql_stitching = "CREATE TABLE $table_stitching (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            production_batch varchar(50) NOT NULL,
            order_id bigint(20) NOT NULL,
            worker_id bigint(20) NOT NULL,
            machine_id bigint(20) DEFAULT NULL,
            target_quantity decimal(12,4) NOT NULL,
            completed_quantity decimal(12,4) DEFAULT 0.0000,
            rejected_quantity decimal(12,4) DEFAULT 0.0000,
            production_date datetime NOT NULL,
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_stitching);

        // 10. Finishing Management
        $table_finishing = $wpdb->prefix . 'garment_finishing';
        $sql_finishing = "CREATE TABLE $table_finishing (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            batch_number varchar(50) NOT NULL,
            order_id bigint(20) NOT NULL,
            process_type varchar(100) NOT NULL, /* Ironing, Thread Cutting, Folding, Packing, Labeling */
            quantity decimal(12,4) NOT NULL,
            completed_quantity decimal(12,4) DEFAULT 0.0000,
            defects_found decimal(12,4) DEFAULT 0.0000,
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_finishing);

        // 11. Workers
        $table_workers = $wpdb->prefix . 'garment_workers';
        $sql_workers = "CREATE TABLE $table_workers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_code varchar(50) NOT NULL UNIQUE,
            name varchar(255) NOT NULL,
            mobile varchar(50) DEFAULT '',
            department varchar(100) DEFAULT '',
            designation varchar(100) DEFAULT '',
            salary_type varchar(50) DEFAULT 'MONTHLY', /* MONTHLY, DAILY, PIECE_RATE */
            daily_wage decimal(12,2) DEFAULT 0.00,
            monthly_salary decimal(12,2) DEFAULT 0.00,
            attendance_status varchar(50) DEFAULT 'ABSENT', /* PRESENT, ABSENT */
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_workers);

        // 12. Payroll
        $table_payroll = $wpdb->prefix . 'garment_payroll';
        $sql_payroll = "CREATE TABLE $table_payroll (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            worker_id bigint(20) NOT NULL,
            month_year varchar(20) NOT NULL, /* e.g. '06-2026' */
            base_salary decimal(12,2) NOT NULL,
            allowance decimal(12,2) DEFAULT 0.00,
            deductions decimal(12,2) DEFAULT 0.00,
            net_salary decimal(12,2) NOT NULL,
            payment_status varchar(50) DEFAULT 'UNPAID', /* PAID, UNPAID */
            payment_date datetime DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_payroll);

        // 13. Quality Control
        $table_quality = $wpdb->prefix . 'garment_quality';
        $sql_quality = "CREATE TABLE $table_quality (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            inspection_number varchar(50) NOT NULL UNIQUE,
            order_id bigint(20) NOT NULL,
            batch_number varchar(50) NOT NULL,
            approved_quantity decimal(12,4) NOT NULL,
            rejected_quantity decimal(12,4) NOT NULL,
            defect_type varchar(255) DEFAULT '', /* Stitching Defect, Fabric Defect, Color Variation, Measurement Defect, Printing Defect */
            remarks text DEFAULT NULL,
            inspection_date datetime NOT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_quality);

        // 14. Wastage Tracking
        $table_wastage = $wpdb->prefix . 'garment_wastage';
        $sql_wastage = "CREATE TABLE $table_wastage (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            department varchar(100) NOT NULL, /* Cutting, Stitching, Finishing, Fabric */
            material_type varchar(100) NOT NULL, /* Fabric, Accessories, Stitching, Finishing */
            quantity decimal(12,4) NOT NULL,
            reason varchar(255) DEFAULT '',
            cost_impact decimal(12,2) DEFAULT 0.00,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_wastage);

        // 15. Dispatch Management
        $table_dispatch = $wpdb->prefix . 'garment_dispatch';
        $sql_dispatch = "CREATE TABLE $table_dispatch (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            dispatch_number varchar(50) NOT NULL UNIQUE,
            order_id bigint(20) NOT NULL,
            customer_name varchar(255) DEFAULT '',
            quantity decimal(12,4) NOT NULL,
            transport_company varchar(255) DEFAULT '',
            tracking_number varchar(100) DEFAULT '',
            dispatch_date datetime NOT NULL,
            delivery_date datetime DEFAULT NULL,
            status varchar(50) DEFAULT 'PENDING',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_dispatch);

        // 16. Inventory Transaction logs
        $table_inventory = $wpdb->prefix . 'garment_inventory';
        $sql_inventory = "CREATE TABLE $table_inventory (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            item_type varchar(50) NOT NULL, /* FABRIC, ACCESSORY, WIP, FINISHED */
            item_id bigint(20) DEFAULT NULL,
            reference varchar(255) DEFAULT '',
            quantity decimal(12,4) NOT NULL,
            movement_type varchar(50) NOT NULL, /* IN, OUT, ADJUSTMENT */
            created_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_inventory);

        // 17. Machine Management
        $table_machines = $wpdb->prefix . 'garment_machines';
        $sql_machines = "CREATE TABLE $table_machines (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            machine_code varchar(50) NOT NULL UNIQUE,
            machine_name varchar(255) NOT NULL,
            machine_type varchar(100) DEFAULT '',
            department varchar(100) DEFAULT '',
            maintenance_due datetime DEFAULT NULL,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_machines);

        // 18. Activity logs
        $table_activity_logs = $wpdb->prefix . 'garment_activity_logs';
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
        remove_role('garment_super_admin');
        remove_role('garment_production_manager');
        remove_role('garment_inventory_manager');
        remove_role('garment_supervisor');
        remove_role('garment_quality_inspector');
        remove_role('garment_dispatch_manager');
        
        add_role('garment_super_admin', 'Garment Super Admin', [
            'read' => true,
            'manage_users' => true,
            'manage_garment_setup' => true,
            'manage_garment_orders' => true,
            'manage_garment_inventory' => true,
            'manage_garment_production' => true,
            'manage_garment_workers' => true,
            'manage_garment_quality' => true,
            'manage_garment_dispatch' => true,
        ]);
        
        add_role('garment_production_manager', 'Garment Production Manager', [
            'read' => true,
            'manage_garment_orders' => true,
            'manage_garment_production' => true,
            'manage_garment_quality' => true,
        ]);
        
        add_role('garment_inventory_manager', 'Garment Inventory Manager', [
            'read' => true,
            'manage_garment_inventory' => true,
        ]);
        
        add_role('garment_supervisor', 'Garment Supervisor', [
            'read' => true,
            'manage_garment_production' => true,
            'manage_garment_workers' => true,
        ]);
        
        add_role('garment_quality_inspector', 'Garment Quality Inspector', [
            'read' => true,
            'manage_garment_quality' => true,
        ]);
        
        add_role('garment_dispatch_manager', 'Garment Dispatch Manager', [
            'read' => true,
            'manage_garment_dispatch' => true,
        ]);
        
        // Bind capabilities to administrator
        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap('manage_users');
            $admin->add_cap('manage_garment_setup');
            $admin->add_cap('manage_garment_orders');
            $admin->add_cap('manage_garment_inventory');
            $admin->add_cap('manage_garment_production');
            $admin->add_cap('manage_garment_workers');
            $admin->add_cap('manage_garment_quality');
            $admin->add_cap('manage_garment_dispatch');
        }
    }
    
    private static function seedDatabase() {
        global $wpdb;
        $now = current_time('mysql');
        
        // 1. Seed standard mock user operators
        $users_to_seed = [
            [
                'username' => 'garmentsuperadmin',
                'password' => '123456',
                'email' => 'garmentadmin@garment.erp',
                'name' => 'Garment Super Admin',
                'role' => 'garment_super_admin'
            ],
            [
                'username' => 'gmt_production',
                'password' => 'productionpass123',
                'email' => 'gmtproduction@garment.erp',
                'name' => 'Garment Production Manager',
                'role' => 'garment_production_manager'
            ],
            [
                'username' => 'gmt_inventory',
                'password' => 'inventorypass123',
                'email' => 'gmtinventory@garment.erp',
                'name' => 'Garment Inventory Manager',
                'role' => 'garment_inventory_manager'
            ],
            [
                'username' => 'gmt_supervisor',
                'password' => 'supervisorpass123',
                'email' => 'gmtsupervisor@garment.erp',
                'name' => 'Garment Supervisor',
                'role' => 'garment_supervisor'
            ],
            [
                'username' => 'gmt_quality',
                'password' => 'qualitypass123',
                'email' => 'gmtquality@garment.erp',
                'name' => 'Garment Quality Inspector',
                'role' => 'garment_quality_inspector'
            ],
            [
                'username' => 'gmt_dispatch',
                'password' => 'dispatchpass123',
                'email' => 'gmtdispatch@garment.erp',
                'name' => 'Garment Dispatch Manager',
                'role' => 'garment_dispatch_manager'
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
                    update_user_meta($user_id, 'garment_user_status', 'APPROVED');
                }
            }
        }
        
        // 2. Seed mock B2B suppliers
        $table_suppliers = $wpdb->prefix . 'garment_suppliers';
        $supplier_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_suppliers");
        if (intval($supplier_count) === 0) {
            $suppliers = [
                [
                    'supplier_name' => 'Vardhman Textiles Ltd',
                    'mobile' => '+919876543220',
                    'email' => 'sales@vardhman.com',
                    'gst_number' => '03AAAAV1111A1Z1',
                    'address' => 'Chandigarh Road, Ludhiana, Punjab',
                    'rating' => 4.80,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'supplier_name' => 'Arvind Limited',
                    'mobile' => '+919876543221',
                    'email' => 'denim@arvind.com',
                    'gst_number' => '24BBBSA2222B2Z2',
                    'address' => 'Naroda Road, Ahmedabad, Gujarat',
                    'rating' => 4.70,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'supplier_name' => 'YKK India Pvt Ltd',
                    'mobile' => '+919876543222',
                    'email' => 'zippers@ykk.in',
                    'gst_number' => '07CCCCY3333C3Z3',
                    'address' => 'Bawal Industrial Area, Rewari, Haryana',
                    'rating' => 4.90,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($suppliers as $supplier) {
                $wpdb->insert($table_suppliers, $supplier);
            }
        }
        
        // 3. Seed mock fabrics
        $table_fabrics = $wpdb->prefix . 'garment_fabrics';
        $fabric_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_fabrics");
        if (intval($fabric_count) === 0) {
            $fabrics = [
                [
                    'fabric_code' => 'FAB-COT-001',
                    'fabric_name' => '100% Pima Cotton Single Jersey',
                    'fabric_type' => 'Cotton',
                    'color' => 'Navy Blue',
                    'gsm' => 180,
                    'width' => 60.00,
                    'available_meters' => 1250.0000,
                    'cost_per_meter' => 220.00,
                    'supplier_id' => 1,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'fabric_code' => 'FAB-DEN-002',
                    'fabric_name' => 'Stretch Denim 12oz',
                    'fabric_type' => 'Denim',
                    'color' => 'Indigo Blue',
                    'gsm' => 340,
                    'width' => 58.00,
                    'available_meters' => 850.0000,
                    'cost_per_meter' => 310.00,
                    'supplier_id' => 2,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($fabrics as $fabric) {
                $wpdb->insert($table_fabrics, $fabric);
            }
        }

        // 4. Seed mock accessories
        $table_accessories = $wpdb->prefix . 'garment_accessories';
        $acc_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_accessories");
        if (intval($acc_count) === 0) {
            $accessories = [
                [
                    'accessory_name' => 'YKK 4cc Metal Zipper (7 inch)',
                    'category' => 'zippers',
                    'available_quantity' => 1500.0000,
                    'unit' => 'PCS',
                    'cost_per_unit' => 12.50,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'accessory_name' => 'Polyester 4-Hole Shirt Button (Navy)',
                    'category' => 'buttons',
                    'available_quantity' => 10000.0000,
                    'unit' => 'PCS',
                    'cost_per_unit' => 0.45,
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($accessories as $acc) {
                $wpdb->insert($table_accessories, $acc);
            }
        }
        
        // 5. Seed mock orders
        $table_orders = $wpdb->prefix . 'garment_orders';
        $order_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_orders");
        if (intval($order_count) === 0) {
            $orders = [
                [
                    'order_number' => 'ORD-2026-001',
                    'customer_name' => 'Inditex Group (Zara)',
                    'product_name' => 'Classic Fit Denim Jeans',
                    'style_code' => 'STYLE-DNM-01',
                    'quantity' => 500.0000,
                    'unit_price' => 1250.00,
                    'delivery_date' => date('Y-m-d H:i:s', strtotime('+45 days')),
                    'status' => 'Pending',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'order_number' => 'ORD-2026-002',
                    'customer_name' => 'Corporate Uniform Solutions',
                    'product_name' => 'Polo Shirt Navy Blue',
                    'style_code' => 'STYLE-POLO-02',
                    'quantity' => 1200.0000,
                    'unit_price' => 450.00,
                    'delivery_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
                    'status' => 'Pending',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($orders as $order) {
                $wpdb->insert($table_orders, $order);
            }
        }
        
        // 6. Seed Bill of Materials (BOM)
        $table_bom = $wpdb->prefix . 'garment_bom';
        $bom_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_bom");
        if (intval($bom_count) === 0) {
            $boms = [
                // Denim Jeans: 1.2 meters fabric, YKK Zipper, YKK Buttons
                [
                    'product_id' => 'STYLE-DNM-01',
                    'fabric_id' => 2,
                    'fabric_requirement' => 1.2500,
                    'accessories_requirement' => json_encode([
                        ['name' => 'YKK 4cc Metal Zipper (7 inch)', 'qty' => 1, 'cost' => 12.50],
                        ['name' => 'Polyester 4-Hole Shirt Button (Navy)', 'qty' => 2, 'cost' => 0.90]
                    ]),
                    'estimated_cost' => 425.00,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                // Polo Shirt: 0.6 meters fabric, Buttons
                [
                    'product_id' => 'STYLE-POLO-02',
                    'fabric_id' => 1,
                    'fabric_requirement' => 0.6500,
                    'accessories_requirement' => json_encode([
                        ['name' => 'Polyester 4-Hole Shirt Button (Navy)', 'qty' => 3, 'cost' => 1.35]
                    ]),
                    'estimated_cost' => 155.00,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($boms as $b) {
                $wpdb->insert($table_bom, $b);
            }
        }
        
        // 7. Seed mock machines
        $table_machines = $wpdb->prefix . 'garment_machines';
        $machine_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_machines");
        if (intval($machine_count) === 0) {
            $machines = [
                [
                    'machine_code' => 'MAC-CUT-01',
                    'machine_name' => 'Gerber Automatic Fabric Cutter',
                    'machine_type' => 'Cutter',
                    'department' => 'Cutting',
                    'maintenance_due' => date('Y-m-d H:i:s', strtotime('+30 days')),
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'machine_code' => 'MAC-STH-02',
                    'machine_name' => 'Juki Single Needle Lockstitch Machine',
                    'machine_type' => 'Stitcher',
                    'department' => 'Stitching',
                    'maintenance_due' => date('Y-m-d H:i:s', strtotime('+15 days')),
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'machine_code' => 'MAC-FIN-03',
                    'machine_name' => 'Ramsay Industrial Steam Iron Station',
                    'machine_type' => 'Ironer',
                    'department' => 'Finishing',
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

        // 8. Seed mock workers
        $table_workers = $wpdb->prefix . 'garment_workers';
        $worker_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_workers");
        if (intval($worker_count) === 0) {
            $workers = [
                [
                    'employee_code' => 'EMP-001',
                    'name' => 'Karan Singh',
                    'mobile' => '+919876543230',
                    'department' => 'Cutting',
                    'designation' => 'Senior Cutter',
                    'salary_type' => 'MONTHLY',
                    'daily_wage' => 0.00,
                    'monthly_salary' => 18000.00,
                    'attendance_status' => 'PRESENT',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'employee_code' => 'EMP-002',
                    'name' => 'Sita Devi',
                    'mobile' => '+919876543231',
                    'department' => 'Stitching',
                    'designation' => 'Stitcher Grade A',
                    'salary_type' => 'PIECE_RATE',
                    'daily_wage' => 0.00,
                    'monthly_salary' => 0.00,
                    'attendance_status' => 'PRESENT',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            foreach ($workers as $worker) {
                $wpdb->insert($table_workers, $worker);
            }
        }
    }
}
