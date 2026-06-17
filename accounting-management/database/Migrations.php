<?php
namespace AccountingManagementApi\Database;

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

        // 1. Customers
        $table_customers = $wpdb->prefix . 'acc_customers';
        $tables[] = "CREATE TABLE $table_customers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            customer_code varchar(50) NOT NULL,
            customer_name varchar(100) NOT NULL,
            mobile varchar(20) DEFAULT '',
            email varchar(100) DEFAULT '',
            address text DEFAULT NULL,
            gst_number varchar(20) DEFAULT '',
            state varchar(50) DEFAULT '',
            credit_limit decimal(10,2) DEFAULT '0.00',
            outstanding_amount decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY customer_code (customer_code)
        ) $charset_collate;";

        // 2. Vendors
        $table_vendors = $wpdb->prefix . 'acc_vendors';
        $tables[] = "CREATE TABLE $table_vendors (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vendor_code varchar(50) NOT NULL,
            vendor_name varchar(100) NOT NULL,
            mobile varchar(20) DEFAULT '',
            email varchar(100) DEFAULT '',
            address text DEFAULT NULL,
            gst_number varchar(20) DEFAULT '',
            state varchar(50) DEFAULT '',
            outstanding_amount decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY vendor_code (vendor_code)
        ) $charset_collate;";

        // 3. Products & Services (Items)
        $table_items = $wpdb->prefix . 'acc_items';
        $tables[] = "CREATE TABLE $table_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            item_code varchar(50) NOT NULL,
            item_name varchar(100) NOT NULL,
            item_type varchar(50) DEFAULT 'Product',
            hsn_sac_code varchar(50) DEFAULT '',
            unit varchar(20) DEFAULT 'PCS',
            purchase_price decimal(10,2) DEFAULT '0.00',
            selling_price decimal(10,2) DEFAULT '0.00',
            gst_percentage decimal(5,2) DEFAULT '18.00',
            stock_quantity int(11) DEFAULT '0',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY item_code (item_code)
        ) $charset_collate;";

        // 4. Sales Invoices
        $table_sales = $wpdb->prefix . 'acc_sales';
        $tables[] = "CREATE TABLE $table_sales (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_number varchar(50) NOT NULL,
            customer_id bigint(20) NOT NULL,
            invoice_date date DEFAULT NULL,
            subtotal decimal(10,2) DEFAULT '0.00',
            cgst decimal(10,2) DEFAULT '0.00',
            sgst decimal(10,2) DEFAULT '0.00',
            igst decimal(10,2) DEFAULT '0.00',
            discount decimal(10,2) DEFAULT '0.00',
            total_amount decimal(10,2) DEFAULT '0.00',
            payment_status varchar(50) DEFAULT 'Unpaid',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY invoice_number (invoice_number)
        ) $charset_collate;";

        // 5. Sale Items
        $table_sale_items = $wpdb->prefix . 'acc_sale_items';
        $tables[] = "CREATE TABLE $table_sale_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            sale_id bigint(20) NOT NULL,
            item_id bigint(20) NOT NULL,
            quantity int(11) DEFAULT '0',
            price decimal(10,2) DEFAULT '0.00',
            gst_percentage decimal(5,2) DEFAULT '0.00',
            gst_amount decimal(10,2) DEFAULT '0.00',
            total_amount decimal(10,2) DEFAULT '0.00',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 6. Purchase Bills
        $table_purchases = $wpdb->prefix . 'acc_purchases';
        $tables[] = "CREATE TABLE $table_purchases (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            purchase_number varchar(50) NOT NULL,
            vendor_id bigint(20) NOT NULL,
            purchase_date date DEFAULT NULL,
            subtotal decimal(10,2) DEFAULT '0.00',
            cgst decimal(10,2) DEFAULT '0.00',
            sgst decimal(10,2) DEFAULT '0.00',
            igst decimal(10,2) DEFAULT '0.00',
            total_amount decimal(10,2) DEFAULT '0.00',
            payment_status varchar(50) DEFAULT 'Unpaid',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY purchase_number (purchase_number)
        ) $charset_collate;";

        // 7. Purchase Items
        $table_purchase_items = $wpdb->prefix . 'acc_purchase_items';
        $tables[] = "CREATE TABLE $table_purchase_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            purchase_id bigint(20) NOT NULL,
            item_id bigint(20) NOT NULL,
            quantity int(11) DEFAULT '0',
            price decimal(10,2) DEFAULT '0.00',
            gst_percentage decimal(5,2) DEFAULT '0.00',
            gst_amount decimal(10,2) DEFAULT '0.00',
            total_amount decimal(10,2) DEFAULT '0.00',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 8. Expenses
        $table_expenses = $wpdb->prefix . 'acc_expenses';
        $tables[] = "CREATE TABLE $table_expenses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            expense_type varchar(100) NOT NULL,
            amount decimal(10,2) DEFAULT '0.00',
            expense_date date DEFAULT NULL,
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 9. Chart of Accounts
        $table_accounts = $wpdb->prefix . 'acc_accounts';
        $tables[] = "CREATE TABLE $table_accounts (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            account_code varchar(50) NOT NULL,
            account_name varchar(100) NOT NULL,
            account_type varchar(50) NOT NULL,
            balance decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY account_code (account_code)
        ) $charset_collate;";

        // 10. Journal Entries
        $table_journals = $wpdb->prefix . 'acc_journals';
        $tables[] = "CREATE TABLE $table_journals (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            journal_number varchar(50) NOT NULL,
            transaction_date date DEFAULT NULL,
            debit_account bigint(20) NOT NULL,
            credit_account bigint(20) NOT NULL,
            amount decimal(10,2) DEFAULT '0.00',
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY journal_number (journal_number)
        ) $charset_collate;";

        // 11. General Ledger Entries
        $table_ledger = $wpdb->prefix . 'acc_ledger';
        $tables[] = "CREATE TABLE $table_ledger (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            account_id bigint(20) NOT NULL,
            transaction_type varchar(10) NOT NULL,
            amount decimal(10,2) DEFAULT '0.00',
            reference_type varchar(50) DEFAULT '',
            reference_id bigint(20) DEFAULT NULL,
            entry_date date DEFAULT NULL,
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 12. GST Summary Records
        $table_gst = $wpdb->prefix . 'acc_gst';
        $tables[] = "CREATE TABLE $table_gst (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_type varchar(50) NOT NULL,
            invoice_id bigint(20) NOT NULL,
            gst_type varchar(20) NOT NULL,
            gst_rate decimal(5,2) DEFAULT '0.00',
            taxable_amount decimal(10,2) DEFAULT '0.00',
            gst_amount decimal(10,2) DEFAULT '0.00',
            tax_period varchar(10) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 13. E-Invoice Logs
        $table_einvoice = $wpdb->prefix . 'acc_einvoice';
        $tables[] = "CREATE TABLE $table_einvoice (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_id bigint(20) NOT NULL,
            irn_number varchar(100) DEFAULT '',
            ack_number varchar(100) DEFAULT '',
            ack_date datetime DEFAULT NULL,
            qr_code text DEFAULT NULL,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 14. E-Way Bills
        $table_ewaybill = $wpdb->prefix . 'acc_ewaybill';
        $tables[] = "CREATE TABLE $table_ewaybill (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_id bigint(20) NOT NULL,
            eway_bill_number varchar(100) NOT NULL,
            vehicle_number varchar(50) DEFAULT '',
            transporter_name varchar(100) DEFAULT '',
            distance int(11) DEFAULT '0',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 15. Stock Adjustments / Inventory Control
        $table_inventory = $wpdb->prefix . 'acc_inventory';
        $tables[] = "CREATE TABLE $table_inventory (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            item_id bigint(20) NOT NULL,
            stock_quantity int(11) DEFAULT '0',
            minimum_stock int(11) DEFAULT '10',
            warehouse varchar(100) DEFAULT 'Default Warehouse',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 16. Payments & Collections
        $table_payments = $wpdb->prefix . 'acc_payments';
        $tables[] = "CREATE TABLE $table_payments (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            payment_type varchar(50) NOT NULL,
            entity_type varchar(50) NOT NULL,
            entity_id bigint(20) NOT NULL,
            reference_type varchar(50) DEFAULT '',
            reference_id bigint(20) DEFAULT NULL,
            amount decimal(10,2) DEFAULT '0.00',
            payment_mode varchar(50) DEFAULT 'Bank',
            payment_date date DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 17. Document Uploads Registry
        $table_documents = $wpdb->prefix . 'acc_documents';
        $tables[] = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            entity_type varchar(50) NOT NULL,
            entity_id bigint(20) NOT NULL,
            document_type varchar(50) NOT NULL,
            file_url varchar(255) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 18. Activity Logs
        $table_logs = $wpdb->prefix . 'acc_activity_logs';
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
        remove_role('accounting_super_admin');
        remove_role('accounting_accountant');
        remove_role('accounting_sales_executive');
        remove_role('accounting_purchase_manager');
        remove_role('accounting_auditor');

        // Full Admin Capability Matrix
        $super_admin_caps = [
            'read' => true,
            'manage_accounting' => true,
            'manage_users' => true,
            'manage_customers' => true,
            'manage_vendors' => true,
            'manage_items' => true,
            'manage_sales' => true,
            'manage_purchases' => true,
            'manage_expenses' => true,
            'manage_accounts' => true,
            'manage_journals' => true,
            'manage_ledgers' => true,
            'manage_gst' => true,
            'manage_einvoice' => true,
            'manage_ewaybill' => true,
            'manage_inventory' => true,
            'manage_payments' => true,
            'view_reports' => true,
            'view_dashboard' => true
        ];

        // Accountant Role
        $accountant_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_sales' => true,
            'manage_purchases' => true,
            'manage_expenses' => true,
            'manage_accounts' => true,
            'manage_journals' => true,
            'manage_ledgers' => true,
            'manage_gst' => true,
            'view_reports' => true
        ];

        // Sales Executive Role
        $sales_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_sales' => true,
            'manage_customers' => true,
            'manage_payments' => true
        ];

        // Purchase Manager Role
        $purchase_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_purchases' => true,
            'manage_vendors' => true
        ];

        // Auditor Role (Read-only reports)
        $auditor_caps = [
            'read' => true,
            'view_dashboard' => true,
            'view_reports' => true
        ];

        add_role('accounting_super_admin', 'Accounting Super Admin', $super_admin_caps);
        add_role('accounting_accountant', 'Accounting Accountant', $accountant_caps);
        add_role('accounting_sales_executive', 'Accounting Sales Executive', $sales_caps);
        add_role('accounting_purchase_manager', 'Accounting Purchase Manager', $purchase_caps);
        add_role('accounting_auditor', 'Accounting Auditor', $auditor_caps);

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
            'asuperadmin' => ['role' => 'accounting_super_admin', 'name' => 'Accounting Super Admin'],
            'aaccountant' => ['role' => 'accounting_accountant', 'name' => 'John Accountant'],
            'asalesexec' => ['role' => 'accounting_sales_executive', 'name' => 'Amit Sales'],
            'apurchasemgr' => ['role' => 'accounting_purchase_manager', 'name' => 'Sarah Purchase'],
            'aauditor' => ['role' => 'accounting_auditor', 'name' => 'Rajesh Auditor']
        ];

        foreach ($users_data as $username => $info) {
            if (!username_exists($username)) {
                $user_id = wp_create_user($username, '123456', $username . '@accounting-erp.com');
                if (!is_wp_error($user_id)) {
                    $user = new \WP_User($user_id);
                    $user->set_role($info['role']);
                    wp_update_user([
                        'ID' => $user_id,
                        'display_name' => $info['name'],
                        'first_name' => explode(' ', $info['name'])[0]
                    ]);
                }
            }
        }

        // Seed 1: Customers
        $table_customers = $wpdb->prefix . 'acc_customers';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_customers")) === 0) {
            $wpdb->insert($table_customers, [
                'customer_code' => 'CUST-ACC-001',
                'customer_name' => 'Acme Corporation Ltd',
                'mobile' => '9892112233',
                'email' => 'finance@acme.com',
                'address' => 'G-Block, Bandra Kurla Complex, Mumbai',
                'gst_number' => '27AAACA1234B1Z0',
                'state' => 'Maharashtra',
                'credit_limit' => 500000.00,
                'outstanding_amount' => 125000.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_customers, [
                'customer_code' => 'CUST-ACC-002',
                'customer_name' => 'Marwari Retailers Delhi',
                'mobile' => '9311002233',
                'email' => 'sales@marwariretail.com',
                'address' => 'Chandni Chowk, Old Delhi',
                'gst_number' => '07AAAAA5566C2Z3',
                'state' => 'Delhi',
                'credit_limit' => 250000.00,
                'outstanding_amount' => 0.00,
                'status' => 'ACTIVE'
            ]);
        }

        // Seed 2: Vendors
        $table_vendors = $wpdb->prefix . 'acc_vendors';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_vendors")) === 0) {
            $wpdb->insert($table_vendors, [
                'vendor_code' => 'VEND-ACC-001',
                'vendor_name' => 'Alpha Steel Distributors',
                'mobile' => '9822334455',
                'email' => 'orders@alphasteel.com',
                'address' => 'MIDC Area, Bhosari, Pune',
                'gst_number' => '27AAACT9988D1Z2',
                'state' => 'Maharashtra',
                'outstanding_amount' => 75000.00,
                'status' => 'ACTIVE'
            ]);
        }

        // Seed 3: Chart of Accounts
        $table_accounts = $wpdb->prefix . 'acc_accounts';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_accounts")) === 0) {
            $wpdb->insert($table_accounts, [
                'account_code' => '1001',
                'account_name' => 'Cash Account',
                'account_type' => 'Asset',
                'balance' => 150000.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_accounts, [
                'account_code' => '1002',
                'account_name' => 'HDFC Bank A/C',
                'account_type' => 'Asset',
                'balance' => 845000.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_accounts, [
                'account_code' => '1200',
                'account_name' => 'Accounts Receivable',
                'account_type' => 'Asset',
                'balance' => 125000.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_accounts, [
                'account_code' => '2100',
                'account_name' => 'Accounts Payable',
                'account_type' => 'Liability',
                'balance' => 75000.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_accounts, [
                'account_code' => '2200',
                'account_name' => 'GST Payable Ledger',
                'account_type' => 'Liability',
                'balance' => 18000.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_accounts, [
                'account_code' => '4000',
                'account_name' => 'Sales Income',
                'account_type' => 'Income',
                'balance' => 0.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_accounts, [
                'account_code' => '5001',
                'account_name' => 'Rent Expense',
                'account_type' => 'Expense',
                'balance' => 0.00,
                'status' => 'ACTIVE'
            ]);
        }

        // Seed 4: Items (Products & Services)
        $table_items = $wpdb->prefix . 'acc_items';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_items")) === 0) {
            $wpdb->insert($table_items, [
                'item_code' => 'PROD-001',
                'item_name' => 'Industrial Steel Angles',
                'item_type' => 'Product',
                'hsn_sac_code' => '7216',
                'unit' => 'TONS',
                'purchase_price' => 45000.00,
                'selling_price' => 55000.00,
                'gst_percentage' => 18.00,
                'stock_quantity' => 120,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_items, [
                'item_code' => 'SERV-001',
                'item_name' => 'Architectural Structuring Consultation',
                'item_type' => 'Service',
                'hsn_sac_code' => '9983',
                'unit' => 'HOURS',
                'purchase_price' => 0.00,
                'selling_price' => 2500.00,
                'gst_percentage' => 18.00,
                'stock_quantity' => 0,
                'status' => 'ACTIVE'
            ]);
        }
    }
}
