<?php
namespace RealEstateManagementApi\Database;

class Migrations {
    
    /**
     * Activate migrations - Creates tables & user roles
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // 1. JWT Secret Setup
        if (!get_option('real_estate_management_jwt_secret')) {
            update_option('real_estate_management_jwt_secret', bin2hex(random_bytes(32)));
        }
        
        // 2. Projects Table
        $table_projects = $wpdb->prefix . 'realestate_projects';
        $sql_projects = "CREATE TABLE $table_projects (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_code varchar(50) NOT NULL,
            project_name varchar(100) NOT NULL,
            location varchar(150) DEFAULT NULL,
            builder_name varchar(100) DEFAULT NULL,
            launch_date date DEFAULT NULL,
            completion_date date DEFAULT NULL,
            status varchar(30) NOT NULL DEFAULT 'Planning',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY project_code (project_code)
        ) $charset_collate;";
        dbDelta($sql_projects);

        // 3. Properties Table
        $table_properties = $wpdb->prefix . 'realestate_properties';
        $sql_properties = "CREATE TABLE $table_properties (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_id bigint(20) NOT NULL,
            project_name varchar(100) DEFAULT NULL,
            tower varchar(50) DEFAULT NULL,
            unit_number varchar(50) NOT NULL,
            property_type varchar(50) NOT NULL DEFAULT 'Apartment',
            area_sqft decimal(10,2) NOT NULL DEFAULT 0.00,
            bedrooms int(11) NOT NULL DEFAULT 0,
            floor int(11) NOT NULL DEFAULT 0,
            price decimal(15,2) NOT NULL DEFAULT 0.00,
            status varchar(30) NOT NULL DEFAULT 'Available',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_properties);

        // 4. Leads Table
        $table_leads = $wpdb->prefix . 'realestate_leads';
        $sql_leads = "CREATE TABLE $table_leads (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_number varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            source varchar(50) NOT NULL DEFAULT 'Website',
            budget decimal(15,2) NOT NULL DEFAULT 0.00,
            property_interest varchar(100) DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            assigned_to bigint(20) DEFAULT NULL,
            lead_status varchar(30) NOT NULL DEFAULT 'New',
            follow_up_date date DEFAULT NULL,
            remarks text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY lead_number (lead_number)
        ) $charset_collate;";
        dbDelta($sql_leads);

        // 5. Site Visits Table
        $table_site_visits = $wpdb->prefix . 'realestate_site_visits';
        $sql_site_visits = "CREATE TABLE $table_site_visits (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) NOT NULL,
            property_id bigint(20) NOT NULL,
            sales_executive_id bigint(20) DEFAULT NULL,
            visit_date date NOT NULL,
            visit_time time NOT NULL,
            transport_required varchar(10) NOT NULL DEFAULT 'No',
            feedback text DEFAULT NULL,
            status varchar(30) NOT NULL DEFAULT 'Scheduled',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_site_visits);

        // 6. Customers Table
        $table_customers = $wpdb->prefix . 'realestate_customers';
        $sql_customers = "CREATE TABLE $table_customers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            customer_code varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            aadhaar_number varchar(50) DEFAULT NULL,
            pan_number varchar(50) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY customer_code (customer_code)
        ) $charset_collate;";
        dbDelta($sql_customers);

        // 7. Bookings Table
        $table_bookings = $wpdb->prefix . 'realestate_bookings';
        $sql_bookings = "CREATE TABLE $table_bookings (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            booking_number varchar(50) NOT NULL,
            customer_id bigint(20) NOT NULL,
            property_id bigint(20) NOT NULL,
            booking_date date NOT NULL,
            booking_amount decimal(15,2) NOT NULL DEFAULT 0.00,
            agreement_value decimal(15,2) NOT NULL DEFAULT 0.00,
            discount decimal(15,2) NOT NULL DEFAULT 0.00,
            final_price decimal(15,2) NOT NULL DEFAULT 0.00,
            broker_id bigint(20) DEFAULT NULL,
            status varchar(30) NOT NULL DEFAULT 'Pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY booking_number (booking_number)
        ) $charset_collate;";
        dbDelta($sql_bookings);

        // 8. Payment Schedules Table
        $table_payment_schedules = $wpdb->prefix . 'realestate_payment_schedules';
        $sql_payment_schedules = "CREATE TABLE $table_payment_schedules (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            booking_id bigint(20) NOT NULL,
            installment_name varchar(100) NOT NULL,
            due_date date NOT NULL,
            amount decimal(15,2) NOT NULL DEFAULT 0.00,
            paid_amount decimal(15,2) NOT NULL DEFAULT 0.00,
            balance_amount decimal(15,2) NOT NULL DEFAULT 0.00,
            payment_status varchar(30) NOT NULL DEFAULT 'Pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_payment_schedules);

        // 9. Brokers Table
        $table_brokers = $wpdb->prefix . 'realestate_brokers';
        $sql_brokers = "CREATE TABLE $table_brokers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            broker_code varchar(50) NOT NULL,
            broker_name varchar(100) NOT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            rera_number varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            commission_percentage decimal(5,2) NOT NULL DEFAULT 0.00,
            status varchar(30) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY broker_code (broker_code)
        ) $charset_collate;";
        dbDelta($sql_brokers);

        // 10. Commissions Table
        $table_commissions = $wpdb->prefix . 'realestate_commissions';
        $sql_commissions = "CREATE TABLE $table_commissions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            broker_id bigint(20) NOT NULL,
            booking_id bigint(20) NOT NULL,
            commission_percentage decimal(5,2) NOT NULL DEFAULT 0.00,
            commission_amount decimal(15,2) NOT NULL DEFAULT 0.00,
            paid_amount decimal(15,2) NOT NULL DEFAULT 0.00,
            balance_amount decimal(15,2) NOT NULL DEFAULT 0.00,
            payment_status varchar(30) NOT NULL DEFAULT 'Pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_commissions);

        // 11. Documents Table
        $table_documents = $wpdb->prefix . 'realestate_documents';
        $sql_documents = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            related_id bigint(20) NOT NULL,
            related_type varchar(30) NOT NULL,
            file_name varchar(255) NOT NULL,
            file_url varchar(255) NOT NULL,
            file_type varchar(100) DEFAULT NULL,
            media_id bigint(20) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_documents);

        // 12. Pipeline Table
        $table_pipeline = $wpdb->prefix . 'realestate_pipeline';
        $sql_pipeline = "CREATE TABLE $table_pipeline (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) NOT NULL,
            stage varchar(50) NOT NULL DEFAULT 'Lead',
            deal_value decimal(15,2) NOT NULL DEFAULT 0.00,
            expected_closure_date date DEFAULT NULL,
            status varchar(30) NOT NULL DEFAULT 'Active',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_pipeline);

        // 13. Registrations Table
        $table_registrations = $wpdb->prefix . 'realestate_registrations';
        $sql_registrations = "CREATE TABLE $table_registrations (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            booking_id bigint(20) NOT NULL,
            registration_date date DEFAULT NULL,
            registration_cost decimal(15,2) NOT NULL DEFAULT 0.00,
            handover_date date DEFAULT NULL,
            status varchar(30) NOT NULL DEFAULT 'Pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_registrations);

        // 14. Activity Logs Table
        $table_logs = $wpdb->prefix . 'realestate_activity_logs';
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

        // Register Custom Roles and Seed Data
        self::register_roles();
        self::seed_test_accounts();
        self::seed_sample_records();
    }
    
    /**
     * Register Custom Real Estate CRM + ERP Roles
     */
    private static function register_roles() {
        remove_role('realestate_super_admin');
        remove_role('realestate_sales_manager');
        remove_role('realestate_sales_executive');
        remove_role('realestate_broker');
        remove_role('realestate_accountant');
        
        $super_admin_caps = [
            'read' => true,
            'manage_realestate' => true,
            'manage_users' => true,
            'manage_leads' => true,
            'manage_properties' => true,
            'manage_projects' => true,
            'manage_site_visits' => true,
            'manage_customers' => true,
            'manage_bookings' => true,
            'manage_payments' => true,
            'manage_brokers' => true,
            'manage_commissions' => true,
            'view_reports' => true,
            'view_dashboard' => true,
        ];
        
        $sales_manager_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_leads' => true,
            'manage_bookings' => true,
            'manage_commissions' => true,
            'view_reports' => true,
        ];
        
        $sales_executive_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_leads' => true, // lead follow-ups
            'manage_site_visits' => true,
            'manage_customers' => true,
        ];
        
        $broker_caps = [
            'read' => true,
            'refer_leads' => true,
            'view_commissions' => true,
            'track_bookings' => true,
        ];
        
        $accountant_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_payments' => true, // installments
            'manage_commissions' => true, // commission settlements
            'view_reports' => true,
        ];
        
        add_role('realestate_super_admin', 'Real Estate Super Admin', $super_admin_caps);
        add_role('realestate_sales_manager', 'Real Estate Sales Manager', $sales_manager_caps);
        add_role('realestate_sales_executive', 'Real Estate Sales Executive', $sales_executive_caps);
        add_role('realestate_broker', 'Real Estate Broker / Channel Partner', $broker_caps);
        add_role('realestate_accountant', 'Real Estate Accountant', $accountant_caps);

        // Ensure WordPress Admin has permissions
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($super_admin_caps as $cap => $grant) {
                $admin_role->add_cap($cap);
            }
        }
    }

    /**
     * Seed credentials for testing roles
     */
    private static function seed_test_accounts() {
        $super_admin_id = username_exists('resuperadmin');
        if ($super_admin_id) {
            wp_set_password('123456', $super_admin_id);
            $user = get_userdata($super_admin_id);
            if (!in_array('realestate_super_admin', $user->roles)) {
                $user->set_role('realestate_super_admin');
            }
            update_user_meta($super_admin_id, 'realestate_user_status', 'APPROVED');
        } else {
            $user_id = wp_insert_user([
                'user_login' => 'resuperadmin',
                'user_email' => 'admin@realestate.erp',
                'user_pass'  => '123456',
                'role'       => 'realestate_super_admin'
            ]);
            if (!is_wp_error($user_id)) {
                update_user_meta($user_id, 'realestate_user_status', 'APPROVED');
            }
        }

        $roles = [
            'remanager'   => ['role' => 'realestate_sales_manager', 'email' => 'manager@realestate.erp'],
            'reexecutive' => ['role' => 'realestate_sales_executive', 'email' => 'executive@realestate.erp'],
            'rebroker'    => ['role' => 'realestate_broker', 'email' => 'broker@realestate.erp'],
            'reaccount'   => ['role' => 'realestate_accountant', 'email' => 'accountant@realestate.erp'],
        ];

        foreach ($roles as $username => $data) {
            $uid = username_exists($username);
            if ($uid) {
                wp_set_password('123456', $uid);
                $user = get_userdata($uid);
                if (!in_array($data['role'], $user->roles)) {
                    $user->set_role($data['role']);
                }
                update_user_meta($uid, 'realestate_user_status', 'APPROVED');
            } else {
                $inserted_uid = wp_insert_user([
                    'user_login' => $username,
                    'user_email' => $data['email'],
                    'user_pass'  => '123456',
                    'role'       => $data['role']
                ]);
                if (!is_wp_error($inserted_uid)) {
                    update_user_meta($inserted_uid, 'realestate_user_status', 'APPROVED');
                }
            }
        }
    }

    /**
     * Seed sample records for demo purposes
     */
    private static function seed_sample_records() {
        global $wpdb;

        // Check if projects already exist
        $table_projects = $wpdb->prefix . 'realestate_projects';
        $count = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_projects");
        if ($count > 0) {
            return; // Seeded already
        }

        // 1. Seed Projects
        $wpdb->insert($table_projects, [
            'project_code' => 'PROJ-MWR-01',
            'project_name' => 'Marwari Heights',
            'location' => 'Sector 45, Gurgaon',
            'builder_name' => 'Marwari Realties Pvt Ltd',
            'launch_date' => '2025-01-15',
            'completion_date' => '2028-12-31',
            'status' => 'In-Progress'
        ]);
        $proj1_id = $wpdb->insert_id;

        $wpdb->insert($table_projects, [
            'project_code' => 'PROJ-MWR-02',
            'project_name' => 'Marwari Elite Villas',
            'location' => 'Sohna Road, Gurgaon',
            'builder_name' => 'Marwari Realties Pvt Ltd',
            'launch_date' => '2025-06-01',
            'completion_date' => '2029-06-01',
            'status' => 'Planning'
        ]);
        $proj2_id = $wpdb->insert_id;

        // 2. Seed Properties
        $table_properties = $wpdb->prefix . 'realestate_properties';
        $wpdb->insert($table_properties, [
            'project_id' => $proj1_id,
            'project_name' => 'Marwari Heights',
            'tower' => 'Tower A',
            'unit_number' => '1001',
            'property_type' => 'Apartment',
            'area_sqft' => 1850.00,
            'bedrooms' => 3,
            'floor' => 10,
            'price' => 12500000.00,
            'status' => 'Available'
        ]);
        $prop1_id = $wpdb->insert_id;

        $wpdb->insert($table_properties, [
            'project_id' => $proj1_id,
            'project_name' => 'Marwari Heights',
            'tower' => 'Tower B',
            'unit_number' => '504',
            'property_type' => 'Apartment',
            'area_sqft' => 1200.00,
            'bedrooms' => 2,
            'floor' => 5,
            'price' => 8500000.00,
            'status' => 'Booked'
        ]);
        $prop2_id = $wpdb->insert_id;

        $wpdb->insert($table_properties, [
            'project_id' => $proj2_id,
            'project_name' => 'Marwari Elite Villas',
            'tower' => 'Villa Block',
            'unit_number' => 'Villa 12',
            'property_type' => 'Villa',
            'area_sqft' => 3500.00,
            'bedrooms' => 4,
            'floor' => 2,
            'price' => 32000000.00,
            'status' => 'Reserved'
        ]);
        $prop3_id = $wpdb->insert_id;

        // 3. Seed Brokers
        $table_brokers = $wpdb->prefix . 'realestate_brokers';
        $wpdb->insert($table_brokers, [
            'broker_code' => 'BRK-RE-501',
            'broker_name' => 'Amit Sharma (Apex Realty)',
            'mobile' => '9876543210',
            'email' => 'amit@apexrealty.com',
            'rera_number' => 'HR/ERA/G/2024/104',
            'address' => 'DLF Phase 3, Gurgaon',
            'commission_percentage' => 2.50,
            'status' => 'ACTIVE'
        ]);
        $broker1_id = $wpdb->insert_id;

        // 4. Seed Leads
        $table_leads = $wpdb->prefix . 'realestate_leads';
        $wpdb->insert($table_leads, [
            'lead_number' => 'LD-2026-0001',
            'name' => 'Rajesh Gupta',
            'mobile' => '9988776655',
            'email' => 'rajesh.gupta@outlook.com',
            'source' => 'Website',
            'budget' => 15000000.00,
            'property_interest' => '3 BHK Apartment',
            'city' => 'Delhi',
            'assigned_to' => null,
            'lead_status' => 'New',
            'remarks' => 'Looking for immediate purchase, preferred higher floors.'
        ]);
        $lead1_id = $wpdb->insert_id;

        $wpdb->insert($table_leads, [
            'lead_number' => 'LD-2026-0002',
            'name' => 'Suman Rao',
            'mobile' => '9122334455',
            'email' => 'suman.rao@gmail.com',
            'source' => 'Broker',
            'budget' => 35000000.00,
            'property_interest' => 'Villa',
            'city' => 'Gurgaon',
            'assigned_to' => null,
            'lead_status' => 'Site Visit Scheduled',
            'remarks' => 'Referred by Amit Sharma (Apex Realty). High net worth lead.'
        ]);
        $lead2_id = $wpdb->insert_id;

        // 5. Seed Site Visits
        $table_site_visits = $wpdb->prefix . 'realestate_site_visits';
        $wpdb->insert($table_site_visits, [
            'lead_id' => $lead2_id,
            'property_id' => $prop3_id,
            'sales_executive_id' => null,
            'visit_date' => current_time('Y-m-d'),
            'visit_time' => '14:30:00',
            'transport_required' => 'Yes',
            'feedback' => 'Loved the sample villa layout. Demanding customized modular kitchen.',
            'status' => 'Completed'
        ]);

        // 6. Seed Customers
        $table_customers = $wpdb->prefix . 'realestate_customers';
        $wpdb->insert($table_customers, [
            'customer_code' => 'CUST-RE-901',
            'name' => 'Sanjay Verma',
            'mobile' => '8899001122',
            'email' => 'sanjay.verma@yahoo.com',
            'address' => 'S-201, Green Park, New Delhi',
            'aadhaar_number' => '1234-5678-9012',
            'pan_number' => 'ABCDE1234F'
        ]);
        $cust1_id = $wpdb->insert_id;

        // 7. Seed Bookings
        $table_bookings = $wpdb->prefix . 'realestate_bookings';
        $wpdb->insert($table_bookings, [
            'booking_number' => 'BKG-2026-0001',
            'customer_id' => $cust1_id,
            'property_id' => $prop2_id,
            'booking_date' => '2026-05-15',
            'booking_amount' => 500000.00,
            'agreement_value' => 8500000.00,
            'discount' => 100000.00,
            'final_price' => 8400000.00,
            'broker_id' => $broker1_id,
            'status' => 'Confirmed'
        ]);
        $bkg1_id = $wpdb->insert_id;

        // 8. Seed Payment Schedules
        $table_payment_schedules = $wpdb->prefix . 'realestate_payment_schedules';
        $wpdb->insert($table_payment_schedules, [
            'booking_id' => $bkg1_id,
            'installment_name' => 'Booking Token Amount',
            'due_date' => '2026-05-15',
            'amount' => 500000.00,
            'paid_amount' => 500000.00,
            'balance_amount' => 0.00,
            'payment_status' => 'Paid'
        ]);
        $wpdb->insert($table_payment_schedules, [
            'booking_id' => $bkg1_id,
            'installment_name' => '1st Installment (Foundation Completion)',
            'due_date' => '2026-08-30',
            'amount' => 2000000.00,
            'paid_amount' => 0.00,
            'balance_amount' => 2000000.00,
            'payment_status' => 'Pending'
        ]);

        // 9. Seed Commissions
        $table_commissions = $wpdb->prefix . 'realestate_commissions';
        $wpdb->insert($table_commissions, [
            'broker_id' => $broker1_id,
            'booking_id' => $bkg1_id,
            'commission_percentage' => 2.50,
            'commission_amount' => 210000.00, // 2.5% of final_price (8,400,000)
            'paid_amount' => 0.00,
            'balance_amount' => 210000.00,
            'payment_status' => 'Pending'
        ]);

        // 10. Seed Pipeline
        $table_pipeline = $wpdb->prefix . 'realestate_pipeline';
        $wpdb->insert($table_pipeline, [
            'lead_id' => $lead1_id,
            'stage' => 'Lead',
            'deal_value' => 12500000.00,
            'expected_closure_date' => '2026-07-15',
            'status' => 'Active'
        ]);
        $wpdb->insert($table_pipeline, [
            'lead_id' => $lead2_id,
            'stage' => 'Negotiation',
            'deal_value' => 32000000.00,
            'expected_closure_date' => '2026-06-30',
            'status' => 'Active'
        ]);
    }
}
