<?php
namespace ServiceManagementApi\Database;

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

        // 1. Leads
        $table_leads = $wpdb->prefix . 'ser_leads';
        $tables[] = "CREATE TABLE $table_leads (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_name varchar(150) NOT NULL,
            customer_name varchar(150) NOT NULL,
            email varchar(100) DEFAULT '',
            phone varchar(20) DEFAULT '',
            status varchar(50) DEFAULT 'Pending',
            source varchar(100) DEFAULT 'Direct',
            requirements text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 2. Quotations
        $table_quotations = $wpdb->prefix . 'ser_quotations';
        $tables[] = "CREATE TABLE $table_quotations (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            quotation_number varchar(50) NOT NULL,
            lead_id bigint(20) DEFAULT NULL,
            customer_name varchar(150) NOT NULL,
            email varchar(100) DEFAULT '',
            phone varchar(20) DEFAULT '',
            quotation_date date DEFAULT NULL,
            total_amount decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Draft',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY quotation_number (quotation_number)
        ) $charset_collate;";

        // 3. Quotation Items
        $table_quotation_items = $wpdb->prefix . 'ser_quotation_items';
        $tables[] = "CREATE TABLE $table_quotation_items (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            quotation_id bigint(20) NOT NULL,
            service_name varchar(255) NOT NULL,
            quantity int(11) DEFAULT '1',
            price decimal(10,2) DEFAULT '0.00',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 4. Jobs
        $table_jobs = $wpdb->prefix . 'ser_jobs';
        $tables[] = "CREATE TABLE $table_jobs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            job_number varchar(50) NOT NULL,
            customer_name varchar(150) NOT NULL,
            phone varchar(20) DEFAULT '',
            address text DEFAULT NULL,
            technician_id bigint(20) DEFAULT NULL,
            quotation_id bigint(20) DEFAULT NULL,
            scheduled_date date DEFAULT NULL,
            status varchar(50) DEFAULT 'Scheduled',
            priority varchar(50) DEFAULT 'Medium',
            description text DEFAULT NULL,
            work_notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY job_number (job_number)
        ) $charset_collate;";

        // 5. AMC (Annual Maintenance Contracts)
        $table_amc = $wpdb->prefix . 'ser_amc';
        $tables[] = "CREATE TABLE $table_amc (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            contract_number varchar(50) NOT NULL,
            customer_name varchar(150) NOT NULL,
            email varchar(100) DEFAULT '',
            phone varchar(20) DEFAULT '',
            start_date date DEFAULT NULL,
            end_date date DEFAULT NULL,
            total_amount decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY contract_number (contract_number)
        ) $charset_collate;";

        // 6. Invoices
        $table_invoices = $wpdb->prefix . 'ser_invoices';
        $tables[] = "CREATE TABLE $table_invoices (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_number varchar(50) NOT NULL,
            job_id bigint(20) DEFAULT NULL,
            amc_id bigint(20) DEFAULT NULL,
            customer_name varchar(150) NOT NULL,
            email varchar(100) DEFAULT '',
            phone varchar(20) DEFAULT '',
            invoice_date date DEFAULT NULL,
            total_amount decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Unpaid',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY invoice_number (invoice_number)
        ) $charset_collate;";

        // 7. Payments
        $table_payments = $wpdb->prefix . 'ser_payments';
        $tables[] = "CREATE TABLE $table_payments (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            payment_number varchar(50) NOT NULL,
            invoice_id bigint(20) NOT NULL,
            amount decimal(10,2) DEFAULT '0.00',
            payment_date date DEFAULT NULL,
            payment_method varchar(50) DEFAULT 'Cash',
            transaction_reference varchar(100) DEFAULT '',
            remarks text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY payment_number (payment_number)
        ) $charset_collate;";

        // 8. Activity Logs
        $table_logs = $wpdb->prefix . 'ser_activity_logs';
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
        remove_role('service_super_admin');
        remove_role('service_manager');
        remove_role('service_technician');
        remove_role('service_customer_care');
        remove_role('service_accountant');

        // Super Admin capabilities
        $super_admin_caps = [
            'read' => true,
            'manage_service_users' => true,
            'manage_leads' => true,
            'manage_quotations' => true,
            'manage_jobs' => true,
            'manage_amc' => true,
            'manage_invoices' => true,
            'manage_payments' => true,
            'view_service_reports' => true,
            'view_service_dashboard' => true,
            'update_assigned_jobs' => true
        ];

        // Manager capabilities
        $manager_caps = [
            'read' => true,
            'view_service_dashboard' => true,
            'manage_leads' => true,
            'manage_quotations' => true,
            'manage_jobs' => true,
            'manage_amc' => true,
            'manage_invoices' => true,
            'manage_payments' => true,
            'view_service_reports' => true
        ];

        // Technician capabilities
        $technician_caps = [
            'read' => true,
            'view_service_dashboard' => true,
            'view_assigned_jobs' => true,
            'update_assigned_jobs' => true
        ];

        // Customer Care capabilities
        $customer_care_caps = [
            'read' => true,
            'view_service_dashboard' => true,
            'manage_leads' => true,
            'manage_amc' => true
        ];

        // Accountant capabilities
        $accountant_caps = [
            'read' => true,
            'view_service_dashboard' => true,
            'manage_quotations' => true,
            'manage_invoices' => true,
            'manage_payments' => true,
            'view_service_reports' => true
        ];

        add_role('service_super_admin', 'Service Super Admin', $super_admin_caps);
        add_role('service_manager', 'Service Manager', $manager_caps);
        add_role('service_technician', 'Service Technician', $technician_caps);
        add_role('service_customer_care', 'Service Customer Care', $customer_care_caps);
        add_role('service_accountant', 'Service Accountant', $accountant_caps);

        // Map all to standard WordPress Administrator role
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($super_admin_caps as $cap => $grant) {
                $admin_role->add_cap($cap);
            }
        }
    }

    private static function seedData() {
        global $wpdb;

        // Seed users
        $users_data = [
            'ssuperadmin' => ['role' => 'service_super_admin', 'name' => 'Service Super Admin'],
            'smanager' => ['role' => 'service_manager', 'name' => 'John Service Manager'],
            'stechnician' => ['role' => 'service_technician', 'name' => 'Ravi Technician'],
            'scustomercare' => ['role' => 'service_customer_care', 'name' => 'Neelam Customer Care'],
            'saccountant' => ['role' => 'service_accountant', 'name' => 'Aakash Accountant']
        ];

        foreach ($users_data as $username => $info) {
            if (!username_exists($username)) {
                $user_id = wp_create_user($username, '123456', $username . '@service-erp.com');
                if (!is_wp_error($user_id)) {
                    $user = new \WP_User($user_id);
                    $user->set_role($info['role']);
                    wp_update_user([
                        'ID' => $user_id,
                        'display_name' => $info['name'],
                        'first_name' => explode(' ', $info['name'])[0]
                    ]);
                    update_user_meta($user_id, 'service_user_status', 'APPROVED');
                }
            }
        }

        // Seed leads
        $table_leads = $wpdb->prefix . 'ser_leads';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_leads")) === 0) {
            $wpdb->insert($table_leads, [
                'lead_name' => 'AC Installation Inquiry',
                'customer_name' => 'Sharma Residency',
                'email' => 'sharma@residency.com',
                'phone' => '9876543210',
                'status' => 'Pending',
                'source' => 'Web',
                'requirements' => 'Requires installation of 3 split AC units in bedrooms.'
            ]);
            $wpdb->insert($table_leads, [
                'lead_name' => 'Annual CCTV Contract',
                'customer_name' => 'Apex Office Spaces',
                'email' => 'contact@apexoffice.com',
                'phone' => '9311002244',
                'status' => 'Qualified',
                'source' => 'Referral',
                'requirements' => 'AMC quotation for 24 CCTV cameras network monitoring.'
            ]);
        }

        // Seed AMC
        $table_amc = $wpdb->prefix . 'ser_amc';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_amc")) === 0) {
            $wpdb->insert($table_amc, [
                'contract_number' => 'AMC-2026-0001',
                'customer_name' => 'Tech Park Sector 62',
                'email' => 'admin@techpark62.com',
                'phone' => '9560112233',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'total_amount' => 45000.00,
                'status' => 'Active'
            ]);
        }

        // Seed Quotations
        $table_quotations = $wpdb->prefix . 'ser_quotations';
        $table_quotation_items = $wpdb->prefix . 'ser_quotation_items';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_quotations")) === 0) {
            $q_id = $wpdb->insert($table_quotations, [
                'quotation_number' => 'QT-2026-0001',
                'lead_id' => 2,
                'customer_name' => 'Apex Office Spaces',
                'email' => 'contact@apexoffice.com',
                'phone' => '9311002244',
                'quotation_date' => '2026-06-10',
                'total_amount' => 15000.00,
                'status' => 'Accepted'
            ]);
            $quotation_id = $wpdb->insert_id;
            if ($quotation_id) {
                $wpdb->insert($table_quotation_items, [
                    'quotation_id' => $quotation_id,
                    'service_name' => 'CCTV Monitoring & Wiring Maintenance Service',
                    'quantity' => 1,
                    'price' => 15000.00
                ]);
            }
        }

        // Seed Jobs
        $table_jobs = $wpdb->prefix . 'ser_jobs';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_jobs")) === 0) {
            // Find technician user ID
            $tech_user = get_user_by('login', 'stechnician');
            $tech_id = $tech_user ? $tech_user->ID : null;

            $wpdb->insert($table_jobs, [
                'job_number' => 'JOB-2026-0001',
                'customer_name' => 'Apex Office Spaces',
                'phone' => '9311002244',
                'address' => 'Plot 12, Sector 63, Noida, UP',
                'technician_id' => $tech_id,
                'quotation_id' => 1,
                'scheduled_date' => '2026-06-20',
                'status' => 'Scheduled',
                'priority' => 'High',
                'description' => 'Deploy initial maintenance wiring inspect and tag CCTV devices.',
                'work_notes' => ''
            ]);
        }
    }
}
