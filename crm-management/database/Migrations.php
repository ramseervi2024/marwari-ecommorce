<?php
namespace CrmManagementApi\Database;

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
        $table_leads = $wpdb->prefix . 'crm_leads';
        $tables[] = "CREATE TABLE $table_leads (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_number varchar(50) NOT NULL,
            first_name varchar(100) DEFAULT '',
            last_name varchar(100) DEFAULT '',
            company_name varchar(150) DEFAULT '',
            mobile varchar(50) DEFAULT '',
            email varchar(100) DEFAULT '',
            website varchar(150) DEFAULT '',
            lead_source varchar(100) DEFAULT 'Website',
            industry varchar(100) DEFAULT '',
            city varchar(100) DEFAULT '',
            state varchar(100) DEFAULT '',
            assigned_to bigint(20) DEFAULT NULL,
            lead_status varchar(50) DEFAULT 'New',
            remarks text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY lead_number (lead_number)
        ) $charset_collate;";

        // 2. Follow-Ups
        $table_followups = $wpdb->prefix . 'crm_followups';
        $tables[] = "CREATE TABLE $table_followups (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) NOT NULL,
            followup_date date NOT NULL,
            followup_time time DEFAULT NULL,
            communication_type varchar(50) DEFAULT 'Call',
            remarks text DEFAULT NULL,
            next_followup_date date DEFAULT NULL,
            status varchar(50) DEFAULT 'Pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 3. Tasks
        $table_tasks = $wpdb->prefix . 'crm_tasks';
        $tables[] = "CREATE TABLE $table_tasks (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(200) NOT NULL,
            description text DEFAULT NULL,
            due_date date NOT NULL,
            status varchar(50) DEFAULT 'Pending',
            priority varchar(50) DEFAULT 'Medium',
            assigned_to bigint(20) DEFAULT NULL,
            lead_id bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 4. Quotations
        $table_quotations = $wpdb->prefix . 'crm_quotations';
        $tables[] = "CREATE TABLE $table_quotations (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            quotation_number varchar(50) NOT NULL,
            lead_id bigint(20) NOT NULL,
            quotation_date date NOT NULL,
            valid_until date NOT NULL,
            subtotal decimal(12,2) DEFAULT '0.00',
            discount decimal(12,2) DEFAULT '0.00',
            tax_amount decimal(12,2) DEFAULT '0.00',
            grand_total decimal(12,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Draft',
            items longtext DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY quotation_number (quotation_number)
        ) $charset_collate;";

        // 5. Customers
        $table_customers = $wpdb->prefix . 'crm_customers';
        $tables[] = "CREATE TABLE $table_customers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            customer_code varchar(50) NOT NULL,
            company_name varchar(150) DEFAULT '',
            contact_person varchar(150) DEFAULT '',
            mobile varchar(50) DEFAULT '',
            email varchar(100) DEFAULT '',
            gst_number varchar(50) DEFAULT '',
            address text DEFAULT NULL,
            city varchar(100) DEFAULT '',
            state varchar(100) DEFAULT '',
            status varchar(50) DEFAULT 'Active',
            user_id bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY customer_code (customer_code)
        ) $charset_collate;";

        // 6. Deals
        $table_deals = $wpdb->prefix . 'crm_deals';
        $tables[] = "CREATE TABLE $table_deals (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            deal_number varchar(50) NOT NULL,
            lead_id bigint(20) NOT NULL,
            customer_id bigint(20) DEFAULT NULL,
            deal_value decimal(12,2) DEFAULT '0.00',
            expected_close_date date DEFAULT NULL,
            deal_stage varchar(50) DEFAULT 'Prospecting',
            probability int(11) DEFAULT '10',
            assigned_to bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY deal_number (deal_number)
        ) $charset_collate;";

        // 7. Call Logs
        $table_call_logs = $wpdb->prefix . 'crm_call_logs';
        $tables[] = "CREATE TABLE $table_call_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) NOT NULL,
            caller_id bigint(20) DEFAULT NULL,
            call_date datetime DEFAULT CURRENT_TIMESTAMP,
            duration int(11) DEFAULT '0',
            notes text DEFAULT NULL,
            recording_url varchar(255) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 8. Meetings
        $table_meetings = $wpdb->prefix . 'crm_meetings';
        $tables[] = "CREATE TABLE $table_meetings (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) NOT NULL,
            host_id bigint(20) DEFAULT NULL,
            title varchar(200) NOT NULL,
            meeting_date date NOT NULL,
            meeting_time time DEFAULT NULL,
            notes text DEFAULT NULL,
            status varchar(50) DEFAULT 'Scheduled',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 9. Invoices
        $table_invoices = $wpdb->prefix . 'crm_invoices';
        $tables[] = "CREATE TABLE $table_invoices (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_number varchar(50) NOT NULL,
            deal_id bigint(20) DEFAULT NULL,
            customer_id bigint(20) NOT NULL,
            invoice_date date NOT NULL,
            due_date date NOT NULL,
            subtotal decimal(12,2) DEFAULT '0.00',
            tax_amount decimal(12,2) DEFAULT '0.00',
            grand_total decimal(12,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Unpaid',
            items longtext DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY invoice_number (invoice_number)
        ) $charset_collate;";

        // 10. Payments
        $table_payments = $wpdb->prefix . 'crm_payments';
        $tables[] = "CREATE TABLE $table_payments (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_id bigint(20) NOT NULL,
            payment_date date NOT NULL,
            amount decimal(12,2) DEFAULT '0.00',
            payment_mode varchar(50) DEFAULT 'Bank Transfer',
            transaction_reference varchar(100) DEFAULT '',
            status varchar(50) DEFAULT 'Success',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 11. WhatsApp Logs
        $table_whatsapp = $wpdb->prefix . 'crm_whatsapp_logs';
        $tables[] = "CREATE TABLE $table_whatsapp (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) DEFAULT NULL,
            message text NOT NULL,
            recipient_number varchar(50) NOT NULL,
            status varchar(50) DEFAULT 'Sent',
            sent_by bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 12. Email Logs
        $table_email = $wpdb->prefix . 'crm_email_logs';
        $tables[] = "CREATE TABLE $table_email (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) DEFAULT NULL,
            subject varchar(200) DEFAULT '',
            message text NOT NULL,
            recipient_email varchar(100) NOT NULL,
            status varchar(50) DEFAULT 'Sent',
            sent_by bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 13. Documents
        $table_documents = $wpdb->prefix . 'crm_documents';
        $tables[] = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) DEFAULT NULL,
            customer_id bigint(20) DEFAULT NULL,
            document_name varchar(150) NOT NULL,
            file_url varchar(255) DEFAULT '',
            status varchar(50) DEFAULT 'Active',
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 14. Activity Logs
        $table_logs = $wpdb->prefix . 'crm_activity_logs';
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
        remove_role('crm_super_admin');
        remove_role('crm_sales_manager');
        remove_role('crm_sales_executive');
        remove_role('crm_telecaller');
        remove_role('crm_customer');

        // Super Admin capabilities
        $super_admin_caps = [
            'read' => true,
            'manage_crm_leads' => true,
            'manage_crm_followups' => true,
            'manage_crm_tasks' => true,
            'manage_crm_quotations' => true,
            'manage_crm_customers' => true,
            'manage_crm_deals' => true,
            'manage_crm_communications' => true,
            'manage_crm_invoices' => true,
            'manage_crm_payments' => true,
            'manage_crm_documents' => true,
            'manage_crm_settings' => true,
            'manage_crm_users' => true,
            'view_crm_dashboard' => true,
            'view_crm_reports' => true,
            'manage_own_crm' => true
        ];

        // Sales Manager capabilities
        $manager_caps = [
            'read' => true,
            'manage_crm_leads' => true,
            'manage_crm_followups' => true,
            'manage_crm_tasks' => true,
            'manage_crm_quotations' => true,
            'manage_crm_customers' => true,
            'manage_crm_deals' => true,
            'manage_crm_communications' => true,
            'manage_crm_invoices' => true,
            'manage_crm_payments' => true,
            'manage_crm_documents' => true,
            'view_crm_dashboard' => true,
            'view_crm_reports' => true,
            'manage_own_crm' => true
        ];

        // Sales Executive capabilities
        $executive_caps = [
            'read' => true,
            'view_crm_dashboard' => true,
            'manage_own_crm' => true
        ];

        // Telecaller capabilities
        $telecaller_caps = [
            'read' => true,
            'view_crm_dashboard' => true,
            'manage_own_crm' => true
        ];

        // Customer capabilities
        $customer_caps = [
            'read' => true,
            'view_crm_customer_portal' => true
        ];

        add_role('crm_super_admin', 'CRM Super Admin', $super_admin_caps);
        add_role('crm_sales_manager', 'CRM Sales Manager', $manager_caps);
        add_role('crm_sales_executive', 'CRM Sales Executive', $executive_caps);
        add_role('crm_telecaller', 'CRM Telecaller', $telecaller_caps);
        add_role('crm_customer', 'CRM Customer', $customer_caps);

        // Map capabilities to administrator role
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
            'csuperadmin' => ['role' => 'crm_super_admin', 'name' => 'CRM Super Admin'],
            'cmanager' => ['role' => 'crm_sales_manager', 'name' => 'Jane Sales Manager'],
            'cexecutive' => ['role' => 'crm_sales_executive', 'name' => 'Rahul Sales Executive'],
            'ctelecaller' => ['role' => 'crm_telecaller', 'name' => 'Sita Telecaller'],
            'ccustomer' => ['role' => 'crm_customer', 'name' => 'Acme Corporation Customer']
        ];

        $user_ids = [];

        foreach ($users_data as $username => $info) {
            if (!username_exists($username)) {
                $user_id = wp_create_user($username, '123456', $username . '@crm-erp.com');
                if (!is_wp_error($user_id)) {
                    $user = new \WP_User($user_id);
                    $user->set_role($info['role']);
                    wp_update_user([
                        'ID' => $user_id,
                        'display_name' => $info['name'],
                        'first_name' => explode(' ', $info['name'])[0]
                    ]);
                    update_user_meta($user_id, 'crm_user_status', 'APPROVED');
                    $user_ids[$info['role']] = $user_id;
                }
            } else {
                $user = get_user_by('login', $username);
                $user_ids[$info['role']] = $user->ID;
            }
        }

        // Seed some sample Leads
        $table_leads = $wpdb->prefix . 'crm_leads';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_leads")) === 0) {
            $leads = [
                [
                    'lead_number' => 'LD-2026-0001',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'company_name' => 'Alpha LLC',
                    'mobile' => '+919876543210',
                    'email' => 'john.doe@alphallc.com',
                    'website' => 'https://alphallc.com',
                    'lead_source' => 'Website',
                    'industry' => 'Technology',
                    'city' => 'Mumbai',
                    'state' => 'Maharashtra',
                    'assigned_to' => $user_ids['crm_sales_executive'] ?? null,
                    'lead_status' => 'New',
                    'remarks' => 'Interested in CRM services.',
                ],
                [
                    'lead_number' => 'LD-2026-0002',
                    'first_name' => 'Amit',
                    'last_name' => 'Sharma',
                    'company_name' => 'Beta Infotech',
                    'mobile' => '+919876543211',
                    'email' => 'amit.sharma@betainfo.com',
                    'website' => 'https://betainfo.com',
                    'lead_source' => 'LinkedIn',
                    'industry' => 'Digital Marketing',
                    'city' => 'Bangalore',
                    'state' => 'Karnataka',
                    'assigned_to' => $user_ids['crm_telecaller'] ?? null,
                    'lead_status' => 'Contacted',
                    'remarks' => 'Needs quotation for SEO services.',
                ],
            ];

            foreach ($leads as $l) {
                $wpdb->insert($table_leads, $l);
            }
        }

        // Seed Customer converted profile for the customer user
        $table_customers = $wpdb->prefix . 'crm_customers';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_customers")) === 0 && isset($user_ids['crm_customer'])) {
            $wpdb->insert($table_customers, [
                'customer_code' => 'CUST-2026-0001',
                'company_name' => 'Acme Corporation',
                'contact_person' => 'Acme Corp Admin',
                'mobile' => '+15551234567',
                'email' => 'ccustomer@crm-erp.com',
                'gst_number' => '27AAAAA1111A1Z1',
                'address' => '123 Acme Business Park',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'status' => 'Active',
                'user_id' => $user_ids['crm_customer'],
            ]);
        }
    }
}
