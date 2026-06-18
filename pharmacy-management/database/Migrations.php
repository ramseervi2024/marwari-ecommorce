<?php
namespace PharmacyErpApi\Database;

if (!defined('ABSPATH')) exit;

class Migrations {
    public static function activate() {
        self::createTables();
        self::setupRoles();
        self::seedData();
    }

    private static function createTables() {
        global $wpdb;
        $c = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $p = $wpdb->prefix;

        // 1. Categories
        dbDelta("CREATE TABLE {$p}pharmacy_categories (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY (id)
        ) $c;");

        // 2. Medicines
        dbDelta("CREATE TABLE {$p}pharmacy_medicines (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            medicine_code varchar(50) NOT NULL,
            name varchar(200) NOT NULL,
            generic_name varchar(200) DEFAULT '',
            category_id bigint(20) DEFAULT NULL,
            manufacturer varchar(150) DEFAULT '',
            unit varchar(30) DEFAULT 'Strip',
            hsn_code varchar(30) DEFAULT '',
            gst_rate decimal(5,2) DEFAULT 5.00,
            mrp decimal(10,2) DEFAULT 0.00,
            sale_price decimal(10,2) DEFAULT 0.00,
            purchase_price decimal(10,2) DEFAULT 0.00,
            reorder_level int DEFAULT 10,
            description text DEFAULT NULL,
            requires_prescription tinyint(1) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY medicine_code (medicine_code)
        ) $c;");

        // 3. Batches
        dbDelta("CREATE TABLE {$p}pharmacy_batches (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            medicine_id bigint(20) NOT NULL,
            batch_number varchar(100) NOT NULL,
            manufacturer varchar(150) DEFAULT '',
            manufacturing_date date DEFAULT NULL,
            expiry_date date NOT NULL,
            purchase_price decimal(10,2) DEFAULT 0.00,
            mrp decimal(10,2) DEFAULT 0.00,
            quantity int DEFAULT 0,
            available_qty int DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $c;");

        // 4. Suppliers
        dbDelta("CREATE TABLE {$p}pharmacy_suppliers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            supplier_code varchar(50) NOT NULL,
            name varchar(200) NOT NULL,
            contact_person varchar(100) DEFAULT '',
            mobile varchar(20) DEFAULT '',
            email varchar(100) DEFAULT '',
            address text DEFAULT NULL,
            city varchar(100) DEFAULT '',
            state varchar(100) DEFAULT '',
            gstin varchar(20) DEFAULT '',
            drug_license varchar(50) DEFAULT '',
            credit_days int DEFAULT 30,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY supplier_code (supplier_code)
        ) $c;");

        // 5. Purchases
        dbDelta("CREATE TABLE {$p}pharmacy_purchases (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            purchase_number varchar(50) NOT NULL,
            supplier_id bigint(20) NOT NULL,
            purchase_date date NOT NULL,
            invoice_number varchar(100) DEFAULT '',
            subtotal decimal(12,2) DEFAULT 0.00,
            discount decimal(12,2) DEFAULT 0.00,
            gst_amount decimal(12,2) DEFAULT 0.00,
            grand_total decimal(12,2) DEFAULT 0.00,
            paid_amount decimal(12,2) DEFAULT 0.00,
            status varchar(30) DEFAULT 'Pending',
            notes text DEFAULT NULL,
            received_by bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY purchase_number (purchase_number)
        ) $c;");

        // 6. Purchase Items
        dbDelta("CREATE TABLE {$p}pharmacy_purchase_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            purchase_id bigint(20) NOT NULL,
            medicine_id bigint(20) NOT NULL,
            batch_number varchar(100) NOT NULL,
            expiry_date date NOT NULL,
            quantity int NOT NULL,
            free_quantity int DEFAULT 0,
            purchase_price decimal(10,2) NOT NULL,
            mrp decimal(10,2) DEFAULT 0.00,
            gst_rate decimal(5,2) DEFAULT 0.00,
            gst_amount decimal(10,2) DEFAULT 0.00,
            discount decimal(10,2) DEFAULT 0.00,
            total decimal(12,2) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $c;");

        // 7. Bills
        dbDelta("CREATE TABLE {$p}pharmacy_bills (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            bill_number varchar(50) NOT NULL,
            customer_name varchar(200) DEFAULT 'Walk-in Customer',
            customer_mobile varchar(20) DEFAULT '',
            doctor_name varchar(150) DEFAULT '',
            bill_date date NOT NULL,
            subtotal decimal(12,2) DEFAULT 0.00,
            discount decimal(12,2) DEFAULT 0.00,
            gst_amount decimal(12,2) DEFAULT 0.00,
            grand_total decimal(12,2) DEFAULT 0.00,
            paid_amount decimal(12,2) DEFAULT 0.00,
            payment_mode varchar(30) DEFAULT 'Cash',
            status varchar(30) DEFAULT 'Paid',
            notes text DEFAULT NULL,
            billed_by bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY bill_number (bill_number)
        ) $c;");

        // 8. Bill Items
        dbDelta("CREATE TABLE {$p}pharmacy_bill_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            bill_id bigint(20) NOT NULL,
            medicine_id bigint(20) NOT NULL,
            batch_id bigint(20) DEFAULT NULL,
            batch_number varchar(100) DEFAULT '',
            expiry_date date DEFAULT NULL,
            quantity int NOT NULL,
            unit_price decimal(10,2) NOT NULL,
            mrp decimal(10,2) DEFAULT 0.00,
            discount decimal(10,2) DEFAULT 0.00,
            gst_rate decimal(5,2) DEFAULT 0.00,
            gst_amount decimal(10,2) DEFAULT 0.00,
            total decimal(12,2) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $c;");

        // 9. Activity Logs
        dbDelta("CREATE TABLE {$p}pharmacy_activity_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            action_type varchar(60) DEFAULT '',
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $c;");
    }

    private static function setupRoles() {
        // Pharmacist role
        add_role('pharmacy_admin', 'Pharmacy Admin', [
            'read' => true,
            'manage_pharmacy' => true,
            'view_pharmacy_reports' => true,
        ]);
        add_role('pharmacy_staff', 'Pharmacy Staff', [
            'read' => true,
            'manage_pharmacy' => true,
        ]);

        // Give admin pharmacy caps
        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap('manage_pharmacy');
            $admin->add_cap('view_pharmacy_reports');
        }
    }

    private static function seedData() {
        global $wpdb;
        $p = $wpdb->prefix;

        // Seed categories
        $cats = ['Tablet', 'Capsule', 'Syrup', 'Injection', 'Cream/Ointment', 'Drops', 'Powder', 'Inhaler', 'Surgical', 'Vitamins'];
        foreach ($cats as $cat) {
            $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$p}pharmacy_categories WHERE name=%s", $cat));
            if (!$exists) {
                $wpdb->insert("{$p}pharmacy_categories", ['name' => $cat]);
            }
        }

        // Seed demo users
        $demo_users = [
            ['username' => 'pharmadmin',  'name' => 'Pharmacy Admin',  'role' => 'pharmacy_admin'],
            ['username' => 'pharmastaff', 'name' => 'Pharmacy Staff',  'role' => 'pharmacy_staff'],
        ];
        foreach ($demo_users as $u) {
            if (!username_exists($u['username'])) {
                $uid = wp_create_user($u['username'], '123456', $u['username'].'@pharmacy.local');
                if (!is_wp_error($uid)) {
                    $user = new \WP_User($uid);
                    $user->set_role($u['role']);
                    wp_update_user(['ID' => $uid, 'display_name' => $u['name']]);
                    update_user_meta($uid, 'pharmacy_user_status', 'APPROVED');
                }
            }
        }

        // Seed 5 sample medicines
        $medicines = [
            ['MED-0001', 'Paracetamol 500mg', 'Paracetamol', 1, 'Strip', '30049011', 12.00, 20.00, 18.00, 10.00, 50],
            ['MED-0002', 'Amoxicillin 250mg', 'Amoxicillin', 2, 'Strip', '30041010', 18.00, 45.00, 40.00, 12.00, 20],
            ['MED-0003', 'Cough Syrup 100ml',  'Dextromethorphan', 3, 'Bottle', '30049099', 12.00, 80.00, 72.00, 45.00, 15],
            ['MED-0004', 'Vitamin C 500mg',    'Ascorbic Acid',   10, 'Strip', '29362700', 12.00, 25.00, 22.00, 15.00, 100],
            ['MED-0005', 'Azithromycin 500mg', 'Azithromycin',    2, 'Strip', '30041020', 18.00, 120.00, 110.00, 75.00, 10],
        ];
        foreach ($medicines as $m) {
            $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$p}pharmacy_medicines WHERE medicine_code=%s", $m[0]));
            if (!$exists) {
                $wpdb->insert("{$p}pharmacy_medicines", [
                    'medicine_code' => $m[0], 'name' => $m[1], 'generic_name' => $m[2],
                    'category_id' => $m[3], 'unit' => $m[4], 'hsn_code' => $m[5],
                    'gst_rate' => $m[6], 'mrp' => $m[7], 'sale_price' => $m[8],
                    'purchase_price' => $m[9], 'reorder_level' => $m[10]
                ]);
            }
        }
    }
}
