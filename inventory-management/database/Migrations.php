<?php
namespace InventoryManagementApi\Database;

if (!defined('ABSPATH')) {
    exit;
}

class Migrations {
    public static function activate() {
        self::createTables();
        self::setupRoles();
        self::seedData();
    }

    private static function createTables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $tables = [];

        // 1. Products
        $table_products = $wpdb->prefix . 'inv_products';
        $tables[] = "CREATE TABLE $table_products (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            sku varchar(50) NOT NULL,
            barcode varchar(100) DEFAULT '',
            product_name varchar(150) NOT NULL,
            description text DEFAULT NULL,
            category varchar(100) DEFAULT 'General',
            brand varchar(100) DEFAULT '',
            unit varchar(20) DEFAULT 'PCS',
            purchase_price decimal(10,2) DEFAULT '0.00',
            selling_price decimal(10,2) DEFAULT '0.00',
            minimum_stock int(11) DEFAULT '10',
            maximum_stock int(11) DEFAULT '1000',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY sku (sku)
        ) $charset_collate;";

        // 2. Warehouses
        $table_warehouses = $wpdb->prefix . 'inv_warehouses';
        $tables[] = "CREATE TABLE $table_warehouses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            warehouse_code varchar(50) NOT NULL,
            warehouse_name varchar(100) NOT NULL,
            location text DEFAULT NULL,
            manager_name varchar(100) DEFAULT '',
            contact_number varchar(20) DEFAULT '',
            capacity int(11) DEFAULT '10000',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY warehouse_code (warehouse_code)
        ) $charset_collate;";

        // 3. Stock
        $table_stock = $wpdb->prefix . 'inv_stock';
        $tables[] = "CREATE TABLE $table_stock (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            warehouse_id bigint(20) NOT NULL,
            available_stock int(11) DEFAULT '0',
            reserved_stock int(11) DEFAULT '0',
            damaged_stock int(11) DEFAULT '0',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY prod_wh (product_id, warehouse_id)
        ) $charset_collate;";

        // 4. Suppliers
        $table_suppliers = $wpdb->prefix . 'inv_suppliers';
        $tables[] = "CREATE TABLE $table_suppliers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            supplier_code varchar(50) NOT NULL,
            supplier_name varchar(100) NOT NULL,
            contact_person varchar(100) DEFAULT '',
            mobile varchar(20) DEFAULT '',
            email varchar(100) DEFAULT '',
            gst_number varchar(20) DEFAULT '',
            address text DEFAULT NULL,
            rating decimal(3,2) DEFAULT '5.00',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY supplier_code (supplier_code)
        ) $charset_collate;";

        // 5. Purchase Orders
        $table_pos = $wpdb->prefix . 'inv_purchase_orders';
        $tables[] = "CREATE TABLE $table_pos (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            po_number varchar(50) NOT NULL,
            supplier_id bigint(20) NOT NULL,
            order_date date DEFAULT NULL,
            total_amount decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY po_number (po_number)
        ) $charset_collate;";

        // 6. Purchase Order Items
        $table_po_items = $wpdb->prefix . 'inv_po_items';
        $tables[] = "CREATE TABLE $table_po_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            po_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            quantity int(11) DEFAULT '0',
            price decimal(10,2) DEFAULT '0.00',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 7. Goods Receipt Notes (GRN)
        $table_grn = $wpdb->prefix . 'inv_grn';
        $tables[] = "CREATE TABLE $table_grn (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            grn_number varchar(50) NOT NULL,
            po_id bigint(20) NOT NULL,
            receive_date date DEFAULT NULL,
            status varchar(50) DEFAULT 'Completed',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY grn_number (grn_number)
        ) $charset_collate;";

        // 8. GRN Items
        $table_grn_items = $wpdb->prefix . 'inv_grn_items';
        $tables[] = "CREATE TABLE $table_grn_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            grn_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            quantity_ordered int(11) DEFAULT '0',
            quantity_received int(11) DEFAULT '0',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 9. Stock Inward (General / Receipts)
        $table_inward = $wpdb->prefix . 'inv_stock_inward';
        $tables[] = "CREATE TABLE $table_inward (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            reference_type varchar(50) DEFAULT '',
            reference_id bigint(20) DEFAULT NULL,
            inward_date date DEFAULT NULL,
            remarks text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 10. Stock Inward Items
        $table_inward_items = $wpdb->prefix . 'inv_inward_items';
        $tables[] = "CREATE TABLE $table_inward_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            inward_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            warehouse_id bigint(20) NOT NULL,
            quantity int(11) DEFAULT '0',
            batch_number varchar(100) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 11. Stock Outward (General / Issues)
        $table_outward = $wpdb->prefix . 'inv_stock_outward';
        $tables[] = "CREATE TABLE $table_outward (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            reference_type varchar(50) DEFAULT '',
            reference_id bigint(20) DEFAULT NULL,
            outward_date date DEFAULT NULL,
            remarks text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 12. Stock Outward Items
        $table_outward_items = $wpdb->prefix . 'inv_outward_items';
        $tables[] = "CREATE TABLE $table_outward_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            outward_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            warehouse_id bigint(20) NOT NULL,
            quantity int(11) DEFAULT '0',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 13. Warehouse Transfers
        $table_transfers = $wpdb->prefix . 'inv_transfers';
        $tables[] = "CREATE TABLE $table_transfers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            transfer_number varchar(50) NOT NULL,
            from_warehouse_id bigint(20) NOT NULL,
            to_warehouse_id bigint(20) NOT NULL,
            transfer_date date DEFAULT NULL,
            status varchar(50) DEFAULT 'Pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY transfer_number (transfer_number)
        ) $charset_collate;";

        // 14. Transfer Items
        $table_transfer_items = $wpdb->prefix . 'inv_transfer_items';
        $tables[] = "CREATE TABLE $table_transfer_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            transfer_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            quantity int(11) DEFAULT '0',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 15. Inventory Audits
        $table_audits = $wpdb->prefix . 'inv_audits';
        $tables[] = "CREATE TABLE $table_audits (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            audit_number varchar(50) NOT NULL,
            warehouse_id bigint(20) NOT NULL,
            audit_date date DEFAULT NULL,
            status varchar(50) DEFAULT 'Pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY audit_number (audit_number)
        ) $charset_collate;";

        // 16. Audit Items
        $table_audit_items = $wpdb->prefix . 'inv_audit_items';
        $tables[] = "CREATE TABLE $table_audit_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            audit_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            system_quantity int(11) DEFAULT '0',
            physical_quantity int(11) DEFAULT '0',
            variance int(11) DEFAULT '0',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 17. Damaged Stock Logs
        $table_damaged = $wpdb->prefix . 'inv_damaged_stock';
        $tables[] = "CREATE TABLE $table_damaged (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            warehouse_id bigint(20) NOT NULL,
            quantity int(11) DEFAULT '0',
            status varchar(50) DEFAULT 'Reported',
            report_date date DEFAULT NULL,
            remarks text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 18. Activity Logs
        $table_logs = $wpdb->prefix . 'inv_activity_logs';
        $tables[] = "CREATE TABLE $table_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            action varchar(100) NOT NULL,
            details text DEFAULT NULL,
            ip_address varchar(50) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        foreach ($tables as $sql) {
            dbDelta($sql);
        }
    }

    private static function setupRoles() {
        remove_role('inventory_super_admin');
        remove_role('inventory_manager');
        remove_role('inventory_purchase_manager');
        remove_role('inventory_warehouse_staff');
        remove_role('inventory_auditor');

        // Full Admin Capability Matrix
        $super_admin_caps = [
            'read' => true,
            'manage_inventory' => true,
            'manage_users' => true,
            'manage_warehouses' => true,
            'manage_suppliers' => true,
            'manage_purchase_orders' => true,
            'manage_grn' => true,
            'manage_transfers' => true,
            'manage_audits' => true,
            'manage_damaged_stock' => true,
            'manage_stock_inward' => true,
            'manage_stock_outward' => true,
            'view_reports' => true,
            'view_dashboard' => true
        ];

        // Manager Role
        $manager_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_inventory' => true,
            'manage_warehouses' => true,
            'manage_audits' => true,
            'manage_damaged_stock' => true,
            'view_reports' => true
        ];

        // Purchase Manager Role
        $purchase_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_purchase_orders' => true,
            'manage_suppliers' => true,
            'manage_grn' => true
        ];

        // Warehouse Staff Role
        $staff_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_stock_inward' => true,
            'manage_stock_outward' => true,
            'manage_transfers' => true
        ];

        // Auditor Role (Read-only reports)
        $auditor_caps = [
            'read' => true,
            'view_dashboard' => true,
            'view_reports' => true
        ];

        add_role('inventory_super_admin', 'Inventory Super Admin', $super_admin_caps);
        add_role('inventory_manager', 'Inventory Manager', $manager_caps);
        add_role('inventory_purchase_manager', 'Inventory Purchase Manager', $purchase_caps);
        add_role('inventory_warehouse_staff', 'Inventory Warehouse Staff', $staff_caps);
        add_role('inventory_auditor', 'Inventory Auditor', $auditor_caps);

        // Ensure WordPress Admin has permissions
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($super_admin_caps as $cap => $grant) {
                $admin_role->add_cap($cap);
            }
        }
    }

    private static function seedData() {
        global $wpdb;

        // Seed Users
        $users_data = [
            'isuperadmin' => ['role' => 'inventory_super_admin', 'name' => 'Inventory Super Admin'],
            'imanager' => ['role' => 'inventory_manager', 'name' => 'John Inventory Manager'],
            'ipurchasemgr' => ['role' => 'inventory_purchase_manager', 'name' => 'Sarah Purchase'],
            'istaff' => ['role' => 'inventory_warehouse_staff', 'name' => 'Mike Warehouse'],
            'iauditor' => ['role' => 'inventory_auditor', 'name' => 'Rajesh Auditor']
        ];

        foreach ($users_data as $username => $info) {
            if (!username_exists($username)) {
                $user_id = wp_create_user($username, '123456', $username . '@inventory-erp.com');
                if (!is_wp_error($user_id)) {
                    $user = new \WP_User($user_id);
                    $user->set_role($info['role']);
                    wp_update_user([
                        'ID' => $user_id,
                        'display_name' => $info['name'],
                        'first_name' => explode(' ', $info['name'])[0]
                    ]);
                    update_user_meta($user_id, 'inventory_user_status', 'APPROVED');
                }
            }
        }

        // Seed 1: Products
        $table_products = $wpdb->prefix . 'inv_products';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_products")) === 0) {
            $wpdb->insert($table_products, [
                'sku' => 'PROD-INV-001',
                'barcode' => '8901030753645',
                'product_name' => 'Heavy Duty Steel Racks',
                'description' => 'Industrial grade modular storage steel racking system.',
                'category' => 'Warehouse Equipment',
                'brand' => 'Apex Steel',
                'unit' => 'SETS',
                'purchase_price' => 7500.00,
                'selling_price' => 12500.00,
                'minimum_stock' => 15,
                'maximum_stock' => 500,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_products, [
                'sku' => 'PROD-INV-002',
                'barcode' => '8901030753699',
                'product_name' => 'Handheld Barcode Scanner 2D',
                'description' => 'Wireless bluetooth laser scanning terminals.',
                'category' => 'Electronics',
                'brand' => 'Zebra Tech',
                'unit' => 'PCS',
                'purchase_price' => 4500.00,
                'selling_price' => 8500.00,
                'minimum_stock' => 5,
                'maximum_stock' => 100,
                'status' => 'ACTIVE'
            ]);
        }

        // Seed 2: Warehouses
        $table_warehouses = $wpdb->prefix . 'inv_warehouses';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_warehouses")) === 0) {
            $wpdb->insert($table_warehouses, [
                'warehouse_code' => 'WH-MUM-001',
                'warehouse_name' => 'Main Mumbai Distribution Hub',
                'location' => 'Plot 45, Nhava Sheva Industrial Area, Navi Mumbai',
                'manager_name' => 'Vijay Kamble',
                'contact_number' => '9819112233',
                'capacity' => 15000,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_warehouses, [
                'warehouse_code' => 'WH-DEL-002',
                'warehouse_name' => 'Delhi NCR Storage Facility',
                'location' => 'Sector 8, Okhla Industrial Area Phase 3, New Delhi',
                'manager_name' => 'Rajesh Sharma',
                'contact_number' => '9311002233',
                'capacity' => 8000,
                'status' => 'ACTIVE'
            ]);
        }

        // Seed 3: Suppliers
        $table_suppliers = $wpdb->prefix . 'inv_suppliers';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_suppliers")) === 0) {
            $wpdb->insert($table_suppliers, [
                'supplier_code' => 'SUP-INV-001',
                'supplier_name' => 'Global Logistical Solutions Ltd',
                'contact_person' => 'Amit Mehta',
                'mobile' => '9822334455',
                'email' => 'sales@globallogistics.com',
                'gst_number' => '27AAACG9988D1Z2',
                'address' => 'Andheri East, Kurla Road, Mumbai',
                'rating' => 4.80,
                'status' => 'ACTIVE'
            ]);
        }

        // Seed 4: Stock
        $table_stock = $wpdb->prefix . 'inv_stock';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_stock")) === 0) {
            $wpdb->insert($table_stock, [
                'product_id' => 1,
                'warehouse_id' => 1,
                'available_stock' => 120,
                'reserved_stock' => 10,
                'damaged_stock' => 2
            ]);
            $wpdb->insert($table_stock, [
                'product_id' => 2,
                'warehouse_id' => 1,
                'available_stock' => 45,
                'reserved_stock' => 5,
                'damaged_stock' => 0
            ]);
        }
    }
}
