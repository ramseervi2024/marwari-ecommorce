<?php
namespace WholesaleErp\Database;

if (!defined('ABSPATH')) exit;

class Migrations {
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $p = $wpdb->prefix;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sqls = [
            // 1. Dealers
            "CREATE TABLE {$p}wholesale_dealers (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                dealer_code varchar(50) NOT NULL,
                dealer_name varchar(200) NOT NULL,
                owner_name varchar(150),
                mobile varchar(20),
                email varchar(100),
                gst_number varchar(20),
                address text,
                city varchar(100),
                state varchar(100),
                pincode varchar(10),
                credit_limit decimal(12,2) DEFAULT 0.00,
                available_credit decimal(12,2) DEFAULT 0.00,
                status varchar(20) DEFAULT 'Active',
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY dealer_code (dealer_code)
            ) $charset_collate;",

            // 2. Products
            "CREATE TABLE {$p}wholesale_products (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                sku varchar(100) NOT NULL,
                barcode varchar(100),
                product_name varchar(200) NOT NULL,
                category varchar(100),
                brand varchar(100),
                unit varchar(50) DEFAULT 'PCS',
                purchase_price decimal(12,2) DEFAULT 0.00,
                mrp decimal(12,2) DEFAULT 0.00,
                selling_price decimal(12,2) DEFAULT 0.00,
                gst_percentage decimal(5,2) DEFAULT 0.00,
                hsn_code varchar(20),
                status varchar(20) DEFAULT 'Active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY sku (sku)
            ) $charset_collate;",

            // 3. Pricing
            "CREATE TABLE {$p}wholesale_pricing (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                product_id bigint(20) unsigned NOT NULL,
                dealer_category varchar(100),
                dealer_id bigint(20) unsigned DEFAULT NULL,
                special_price decimal(12,2) DEFAULT 0.00,
                discount_percentage decimal(5,2) DEFAULT 0.00,
                min_quantity int(11) DEFAULT 1,
                effective_date date,
                expiry_date date,
                scheme_name varchar(100),
                status varchar(20) DEFAULT 'Active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 4. Orders
            "CREATE TABLE {$p}wholesale_orders (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                order_number varchar(50) NOT NULL,
                dealer_id bigint(20) unsigned NOT NULL,
                sales_rep_id bigint(20) unsigned DEFAULT NULL,
                order_date date NOT NULL,
                total_amount decimal(12,2) DEFAULT 0.00,
                discount_amount decimal(12,2) DEFAULT 0.00,
                gst_amount decimal(12,2) DEFAULT 0.00,
                net_amount decimal(12,2) DEFAULT 0.00,
                order_status varchar(30) DEFAULT 'Draft',
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY order_number (order_number)
            ) $charset_collate;",

            // 5. Order Items
            "CREATE TABLE {$p}wholesale_order_items (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                order_id bigint(20) unsigned NOT NULL,
                product_id bigint(20) unsigned NOT NULL,
                quantity int(11) DEFAULT 1,
                unit_price decimal(12,2) DEFAULT 0.00,
                discount decimal(5,2) DEFAULT 0.00,
                gst_percentage decimal(5,2) DEFAULT 0.00,
                gst_amount decimal(12,2) DEFAULT 0.00,
                total decimal(12,2) DEFAULT 0.00,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 6. Sales Representatives
            "CREATE TABLE {$p}wholesale_sales_reps (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                employee_code varchar(50) NOT NULL,
                full_name varchar(150) NOT NULL,
                mobile varchar(20),
                email varchar(100),
                territory varchar(100),
                target_amount decimal(12,2) DEFAULT 0.00,
                achieved_amount decimal(12,2) DEFAULT 0.00,
                status varchar(20) DEFAULT 'Active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY employee_code (employee_code)
            ) $charset_collate;",

            // 7. Routes
            "CREATE TABLE {$p}wholesale_routes (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                route_name varchar(150) NOT NULL,
                sales_rep_id bigint(20) unsigned DEFAULT NULL,
                area varchar(100),
                beat_day varchar(50),
                total_dealers int(11) DEFAULT 0,
                status varchar(20) DEFAULT 'Active',
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 8. Warehouses
            "CREATE TABLE {$p}wholesale_warehouses (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                warehouse_name varchar(150) NOT NULL,
                warehouse_code varchar(50),
                address text,
                city varchar(100),
                state varchar(100),
                manager_name varchar(150),
                contact varchar(20),
                status varchar(20) DEFAULT 'Active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 9. Inventory
            "CREATE TABLE {$p}wholesale_inventory (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                product_id bigint(20) unsigned NOT NULL,
                warehouse_id bigint(20) unsigned NOT NULL,
                available_stock int(11) DEFAULT 0,
                reserved_stock int(11) DEFAULT 0,
                damaged_stock int(11) DEFAULT 0,
                minimum_stock int(11) DEFAULT 0,
                batch_number varchar(50),
                expiry_date date,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY product_warehouse (product_id, warehouse_id)
            ) $charset_collate;",

            // 10. Dispatches
            "CREATE TABLE {$p}wholesale_dispatches (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                dispatch_number varchar(50) NOT NULL,
                order_id bigint(20) unsigned NOT NULL,
                vehicle_number varchar(30),
                driver_name varchar(100),
                driver_mobile varchar(20),
                dispatch_date date,
                expected_delivery_date date,
                actual_delivery_date date,
                status varchar(30) DEFAULT 'Pending',
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY dispatch_number (dispatch_number)
            ) $charset_collate;",

            // 11. Credit Limits
            "CREATE TABLE {$p}wholesale_credit_limits (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                dealer_id bigint(20) unsigned NOT NULL,
                credit_limit decimal(12,2) DEFAULT 0.00,
                used_credit decimal(12,2) DEFAULT 0.00,
                available_credit decimal(12,2) DEFAULT 0.00,
                approval_status varchar(30) DEFAULT 'Pending',
                approved_by bigint(20) unsigned DEFAULT NULL,
                approved_at datetime DEFAULT NULL,
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 12. Payments
            "CREATE TABLE {$p}wholesale_payments (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                receipt_number varchar(50) NOT NULL,
                dealer_id bigint(20) unsigned NOT NULL,
                invoice_id bigint(20) unsigned DEFAULT NULL,
                payment_date date NOT NULL,
                amount decimal(12,2) DEFAULT 0.00,
                payment_method varchar(30) DEFAULT 'Cash',
                reference_number varchar(100),
                bank_name varchar(100),
                cheque_number varchar(50),
                status varchar(20) DEFAULT 'Received',
                notes text,
                collected_by bigint(20) unsigned DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY receipt_number (receipt_number)
            ) $charset_collate;",

            // 13. Outstandings
            "CREATE TABLE {$p}wholesale_outstandings (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                dealer_id bigint(20) unsigned NOT NULL,
                invoice_id bigint(20) unsigned DEFAULT NULL,
                invoice_number varchar(50),
                invoice_date date,
                due_date date,
                amount decimal(12,2) DEFAULT 0.00,
                paid_amount decimal(12,2) DEFAULT 0.00,
                balance decimal(12,2) DEFAULT 0.00,
                days_overdue int(11) DEFAULT 0,
                status varchar(30) DEFAULT 'Pending',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 14. Suppliers
            "CREATE TABLE {$p}wholesale_suppliers (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                supplier_code varchar(50),
                supplier_name varchar(200) NOT NULL,
                contact_person varchar(150),
                mobile varchar(20),
                email varchar(100),
                gst_number varchar(20),
                address text,
                city varchar(100),
                state varchar(100),
                credit_days int(11) DEFAULT 0,
                status varchar(20) DEFAULT 'Active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 15. Purchases
            "CREATE TABLE {$p}wholesale_purchases (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                purchase_number varchar(50) NOT NULL,
                supplier_id bigint(20) unsigned NOT NULL,
                warehouse_id bigint(20) unsigned DEFAULT NULL,
                purchase_date date NOT NULL,
                total_amount decimal(12,2) DEFAULT 0.00,
                gst_amount decimal(12,2) DEFAULT 0.00,
                net_amount decimal(12,2) DEFAULT 0.00,
                status varchar(20) DEFAULT 'Draft',
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY purchase_number (purchase_number)
            ) $charset_collate;",

            // 16. Billing (Invoices)
            "CREATE TABLE {$p}wholesale_billing (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                invoice_number varchar(50) NOT NULL,
                dealer_id bigint(20) unsigned NOT NULL,
                order_id bigint(20) unsigned DEFAULT NULL,
                invoice_date date NOT NULL,
                due_date date,
                subtotal decimal(12,2) DEFAULT 0.00,
                discount_amount decimal(12,2) DEFAULT 0.00,
                gst_amount decimal(12,2) DEFAULT 0.00,
                net_amount decimal(12,2) DEFAULT 0.00,
                paid_amount decimal(12,2) DEFAULT 0.00,
                balance decimal(12,2) DEFAULT 0.00,
                invoice_type varchar(20) DEFAULT 'Invoice',
                status varchar(20) DEFAULT 'Unpaid',
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY invoice_number (invoice_number)
            ) $charset_collate;",

            // 17. Activity Logs
            "CREATE TABLE {$p}wholesale_activity_logs (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                action varchar(100) NOT NULL,
                details text,
                ip_address varchar(50),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 18. Documents
            "CREATE TABLE {$p}wholesale_documents (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                reference_type varchar(50),
                reference_id bigint(20) unsigned,
                file_name varchar(255),
                file_path varchar(500),
                file_type varchar(50),
                file_size int(11),
                uploaded_by bigint(20) unsigned,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;"
        ];

        foreach ($sqls as $sql) {
            dbDelta($sql);
        }

        self::seedRoles();
        self::seedData();
    }

    private static function seedRoles() {
        $roles = [
            'wholesale_super_admin'       => 'Wholesale Super Admin',
            'wholesale_dist_manager'      => 'Wholesale Distribution Manager',
            'wholesale_sales_exec'        => 'Wholesale Sales Executive',
            'wholesale_warehouse_manager' => 'Wholesale Warehouse Manager',
            'wholesale_accountant'        => 'Wholesale Accountant',
            'wholesale_dealer'            => 'Wholesale Dealer',
        ];
        foreach ($roles as $slug => $name) {
            if (!get_role($slug)) {
                add_role($slug, $name, ['read' => true]);
            }
        }
    }

    private static function seedData() {
        // Create default admin user
        $users = [
            ['wholesale_admin', 'admin@wholesale.local', 'admin123', 'wholesale_super_admin', 'Wholesale Admin'],
            ['wholesale_manager', 'manager@wholesale.local', 'manager123', 'wholesale_dist_manager', 'Distribution Manager'],
            ['wholesale_sales', 'sales@wholesale.local', 'sales123', 'wholesale_sales_exec', 'Sales Executive'],
            ['wholesale_accountant', 'accounts@wholesale.local', 'accounts123', 'wholesale_accountant', 'Accountant'],
        ];
        foreach ($users as $u) {
            if (!username_exists($u[0])) {
                $uid = wp_create_user($u[0], $u[2], $u[1]);
                if (!is_wp_error($uid)) {
                    $user = new \WP_User($uid);
                    $user->set_role($u[3]);
                    wp_update_user(['ID' => $uid, 'display_name' => $u[4]]);
                    update_user_meta($uid, 'wholesale_user_status', 'APPROVED');
                }
            }
        }

        // Seed sample warehouse
        global $wpdb;
        $p = $wpdb->prefix;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$p}wholesale_warehouses");
        if ($count == 0) {
            $wpdb->insert($p . 'wholesale_warehouses', [
                'warehouse_name' => 'Main Warehouse',
                'warehouse_code' => 'WH-001',
                'city'           => 'Mumbai',
                'state'          => 'Maharashtra',
                'manager_name'   => 'Warehouse Admin',
                'status'         => 'Active',
            ]);
        }
    }
}
