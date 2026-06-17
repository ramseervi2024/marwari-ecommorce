<?php
namespace ConstructionManagementApi\Database;

class Migrations {
    
    /**
     * Activate migrations - Creates tables & user roles
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // 1. JWT Secret Setup
        if (!get_option('construction_management_jwt_secret')) {
            update_option('construction_management_jwt_secret', bin2hex(random_bytes(32)));
        }
        
        // 2. Projects Table
        $table_projects = $wpdb->prefix . 'construction_projects';
        $sql_projects = "CREATE TABLE $table_projects (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_code varchar(50) NOT NULL,
            project_name varchar(100) NOT NULL,
            client_name varchar(100) DEFAULT NULL,
            project_type varchar(50) DEFAULT NULL,
            location varchar(150) DEFAULT NULL,
            start_date date DEFAULT NULL,
            end_date date DEFAULT NULL,
            estimated_cost decimal(15,2) NOT NULL DEFAULT 0.00,
            actual_cost decimal(15,2) NOT NULL DEFAULT 0.00,
            project_manager varchar(100) DEFAULT NULL,
            status varchar(30) NOT NULL DEFAULT 'Planning',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY project_code (project_code)
        ) $charset_collate;";
        dbDelta($sql_projects);

        // 3. Milestones Table
        $table_milestones = $wpdb->prefix . 'construction_milestones';
        $sql_milestones = "CREATE TABLE $table_milestones (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_id bigint(20) NOT NULL,
            milestone_name varchar(100) NOT NULL,
            planned_date date DEFAULT NULL,
            actual_date date DEFAULT NULL,
            completion_percentage decimal(5,2) NOT NULL DEFAULT 0.00,
            status varchar(30) NOT NULL DEFAULT 'Pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_milestones);

        // 4. Materials Table
        $table_materials = $wpdb->prefix . 'construction_materials';
        $sql_materials = "CREATE TABLE $table_materials (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            material_code varchar(50) NOT NULL,
            material_name varchar(100) NOT NULL,
            unit varchar(20) NOT NULL,
            available_quantity decimal(12,2) NOT NULL DEFAULT 0.00,
            minimum_stock decimal(12,2) NOT NULL DEFAULT 0.00,
            purchase_price decimal(10,2) NOT NULL DEFAULT 0.00,
            supplier_id bigint(20) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY material_code (material_code)
        ) $charset_collate;";
        dbDelta($sql_materials);

        // 5. Purchases Table (Material Purchase Orders)
        $table_purchases = $wpdb->prefix . 'construction_purchases';
        $sql_purchases = "CREATE TABLE $table_purchases (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            purchase_order_number varchar(50) NOT NULL,
            project_id bigint(20) NOT NULL,
            supplier_id bigint(20) NOT NULL,
            material_id bigint(20) NOT NULL,
            quantity decimal(12,2) NOT NULL,
            rate decimal(10,2) NOT NULL,
            gst_amount decimal(12,2) NOT NULL DEFAULT 0.00,
            total_amount decimal(12,2) NOT NULL DEFAULT 0.00,
            purchase_date date NOT NULL,
            status varchar(30) NOT NULL DEFAULT 'Pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY purchase_order_number (purchase_order_number)
        ) $charset_collate;";
        dbDelta($sql_purchases);

        // 6. Suppliers Table
        $table_suppliers = $wpdb->prefix . 'construction_suppliers';
        $sql_suppliers = "CREATE TABLE $table_suppliers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            supplier_name varchar(100) NOT NULL,
            contact_person varchar(100) DEFAULT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            gst_number varchar(50) DEFAULT NULL,
            address text DEFAULT NULL,
            rating decimal(3,2) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_suppliers);

        // 7. Site Expenses Table
        $table_expenses = $wpdb->prefix . 'construction_site_expenses';
        $sql_expenses = "CREATE TABLE $table_expenses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_id bigint(20) NOT NULL,
            expense_type varchar(50) NOT NULL, -- Fuel, Rent, Maintenance, Utility, Water, Transport, etc.
            amount decimal(12,2) NOT NULL DEFAULT 0.00,
            expense_date date NOT NULL,
            description text DEFAULT NULL,
            approved_by varchar(100) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_expenses);

        // 8. Contractors Table
        $table_contractors = $wpdb->prefix . 'construction_contractors';
        $sql_contractors = "CREATE TABLE $table_contractors (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            contractor_code varchar(50) NOT NULL,
            contractor_name varchar(100) NOT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            specialization varchar(100) DEFAULT NULL,
            contract_value decimal(15,2) NOT NULL DEFAULT 0.00,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY contractor_code (contractor_code)
        ) $charset_collate;";
        dbDelta($sql_contractors);

        // 9. Labour Table
        $table_labours = $wpdb->prefix . 'construction_labours';
        $sql_labours = "CREATE TABLE $table_labours (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_code varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            mobile varchar(20) DEFAULT NULL,
            trade varchar(50) DEFAULT NULL, -- Mason, Carpenter, Electrician, Plumber, Painter, Helper
            daily_wage decimal(10,2) NOT NULL DEFAULT 0.00,
            attendance_status varchar(30) DEFAULT 'ABSENT',
            project_id bigint(20) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY employee_code (employee_code)
        ) $charset_collate;";
        dbDelta($sql_labours);

        // 10. Attendance Table
        $table_attendance = $wpdb->prefix . 'construction_attendance';
        $sql_attendance = "CREATE TABLE $table_attendance (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            labour_id bigint(20) NOT NULL,
            project_id bigint(20) NOT NULL,
            attendance_date date NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'Present', -- Present, Absent, Half Day
            working_hours decimal(4,2) NOT NULL DEFAULT 8.00,
            overtime_hours decimal(4,2) NOT NULL DEFAULT 0.00,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_attendance);

        // 11. Payroll Table
        $table_payroll = $wpdb->prefix . 'construction_payroll';
        $sql_payroll = "CREATE TABLE $table_payroll (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            labour_id bigint(20) NOT NULL,
            project_id bigint(20) NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            total_days_worked int(11) NOT NULL DEFAULT 0,
            regular_earnings decimal(12,2) NOT NULL DEFAULT 0.00,
            overtime_earnings decimal(12,2) NOT NULL DEFAULT 0.00,
            total_earnings decimal(12,2) NOT NULL DEFAULT 0.00,
            payment_status varchar(20) NOT NULL DEFAULT 'Unpaid', -- Paid, Unpaid, Processing
            payment_date date DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_payroll);

        // 12. Progress Tracking Table
        $table_progress = $wpdb->prefix . 'construction_progress';
        $sql_progress = "CREATE TABLE $table_progress (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_id bigint(20) NOT NULL,
            work_category varchar(100) NOT NULL, -- Foundation, Brickwork, Plastering, Electrical, Plumbing, Painting
            planned_percentage decimal(5,2) NOT NULL DEFAULT 0.00,
            actual_percentage decimal(5,2) NOT NULL DEFAULT 0.00,
            remarks text DEFAULT NULL,
            photos text DEFAULT NULL, -- Comma-separated or serialized URLs
            update_date date NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_progress);

        // 13. Equipment Table
        $table_equipment = $wpdb->prefix . 'construction_equipment';
        $sql_equipment = "CREATE TABLE $table_equipment (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            equipment_code varchar(50) NOT NULL,
            equipment_name varchar(100) NOT NULL,
            purchase_cost decimal(12,2) NOT NULL DEFAULT 0.00,
            rental_cost decimal(12,2) NOT NULL DEFAULT 0.00,
            location varchar(150) DEFAULT NULL,
            maintenance_due date DEFAULT NULL,
            status varchar(30) NOT NULL DEFAULT 'Available', -- Available, Rented, Maintenance, In-Use
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY equipment_code (equipment_code)
        ) $charset_collate;";
        dbDelta($sql_equipment);

        // 14. Billing Table
        $table_billing = $wpdb->prefix . 'construction_billing';
        $sql_billing = "CREATE TABLE $table_billing (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_number varchar(50) NOT NULL,
            project_id bigint(20) NOT NULL,
            client_name varchar(100) NOT NULL,
            milestone_name varchar(100) DEFAULT NULL,
            invoice_amount decimal(15,2) NOT NULL DEFAULT 0.00,
            gst_amount decimal(15,2) NOT NULL DEFAULT 0.00,
            payment_status varchar(30) NOT NULL DEFAULT 'PENDING', -- PAID, PENDING, OVERDUE
            invoice_date date NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY invoice_number (invoice_number)
        ) $charset_collate;";
        dbDelta($sql_billing);

        // 15. Documents Table
        $table_documents = $wpdb->prefix . 'construction_documents';
        $sql_documents = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            related_id bigint(20) NOT NULL,
            related_type varchar(30) NOT NULL, -- PROJECT, CONTRACTOR, PURCHASE, EXPENSE
            document_type varchar(50) NOT NULL, -- Drawing, BOQ, Contract, Site Photo, Bill Scan
            file_url varchar(255) NOT NULL,
            media_id bigint(20) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_documents);

        // 16. Activity Logs Table
        $table_logs = $wpdb->prefix . 'construction_activity_logs';
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
     * Register Custom Construction ERP Roles
     */
    private static function register_roles() {
        remove_role('construction_super_admin');
        remove_role('construction_project_manager');
        remove_role('construction_site_engineer');
        remove_role('construction_purchase_manager');
        remove_role('construction_contractor');
        remove_role('construction_accountant');
        
        $super_admin_caps = [
            'read' => true,
            'manage_construction' => true,
            'manage_users' => true,
            'manage_projects' => true,
            'manage_materials' => true,
            'manage_expenses' => true,
            'manage_contractors' => true,
            'manage_labour' => true,
            'manage_progress' => true,
            'manage_equipment' => true,
            'manage_billing' => true,
            'view_reports' => true,
            'view_dashboard' => true,
        ];
        
        $project_manager_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_projects' => true,
            'manage_progress' => true,
            'manage_contractors' => true,
        ];
        
        $site_engineer_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_progress' => true,
            'manage_labour' => true,
            'manage_materials' => true,
        ];
        
        $purchase_manager_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_materials' => true,
        ];
        
        $contractor_caps = [
            'read' => true,
            'view_assigned_work' => true,
        ];
        
        $accountant_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_expenses' => true,
            'manage_billing' => true,
            'view_reports' => true,
        ];
        
        add_role('construction_super_admin', 'Construction Super Admin', $super_admin_caps);
        add_role('construction_project_manager', 'Construction Project Manager', $project_manager_caps);
        add_role('construction_site_engineer', 'Construction Site Engineer', $site_engineer_caps);
        add_role('construction_purchase_manager', 'Construction Purchase Manager', $purchase_manager_caps);
        add_role('construction_contractor', 'Construction Contractor', $contractor_caps);
        add_role('construction_accountant', 'Construction Accountant', $accountant_caps);

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
        $super_admin_id = username_exists('constsuperadmin');
        if ($super_admin_id) {
            wp_set_password('123456', $super_admin_id);
            $user = get_userdata($super_admin_id);
            if (!in_array('construction_super_admin', $user->roles)) {
                $user->set_role('construction_super_admin');
            }
            update_user_meta($super_admin_id, 'construction_user_status', 'APPROVED');
        } else {
            $user_id = wp_insert_user([
                'user_login' => 'constsuperadmin',
                'user_email' => 'admin@construction.erp',
                'user_pass' => '123456',
                'display_name' => 'Construction Super Admin',
                'first_name' => 'Construction Super Admin',
                'role' => 'construction_super_admin'
            ]);
            if (!is_wp_error($user_id)) {
                update_user_meta($user_id, 'construction_user_status', 'APPROVED');
            }
        }

        self::create_test_user('constprojectmanager', 'pm@construction.erp', 'pmtest123', 'Construction PM', 'construction_project_manager');
        self::create_test_user('constsiteengineer', 'engineer@construction.erp', 'engineertest123', 'Construction Site Engineer', 'construction_site_engineer');
        self::create_test_user('constpurchasemanager', 'purchase@construction.erp', 'purchasetest123', 'Construction Purchase Manager', 'construction_purchase_manager');
        self::create_test_user('constcontractor', 'contractor@construction.erp', 'contractortest123', 'Construction Contractor', 'construction_contractor');
        self::create_test_user('constaccountant', 'accountant@construction.erp', 'accountanttest123', 'Construction Accountant', 'construction_accountant');
    }

    private static function create_test_user(string $username, string $email, string $password, string $display_name, string $role) {
        $user_id = username_exists($username);
        if (!$user_id && !email_exists($email)) {
            $user_id = wp_insert_user([
                'user_login' => $username,
                'user_email' => $email,
                'user_pass' => $password,
                'display_name' => $display_name,
                'first_name' => $display_name,
                'role' => $role
            ]);
        }
        if ($user_id && !is_wp_error($user_id)) {
            update_user_meta($user_id, 'construction_user_status', 'APPROVED');
        }
    }

    /**
     * Seed initial sample construction records for API testing
     */
    private static function seed_sample_records() {
        global $wpdb;

        // 1. Seed Projects
        $table_projects = $wpdb->prefix . 'construction_projects';
        if ((int)$wpdb->get_var("SELECT COUNT(*) FROM $table_projects") === 0) {
            $wpdb->insert($table_projects, [
                'project_code' => 'PRJ-2026-001',
                'project_name' => 'Marwari Heights Residential Complex',
                'client_name' => 'Marwari Realties Pvt Ltd',
                'project_type' => 'Residential Complexes',
                'location' => 'Sector 62, Noida, Uttar Pradesh',
                'start_date' => '2026-01-15',
                'end_date' => '2027-12-31',
                'estimated_cost' => 50000000.00,
                'actual_cost' => 12500000.00,
                'project_manager' => 'Construction PM',
                'status' => 'Active'
            ]);
            $proj1_id = $wpdb->insert_id;

            $wpdb->insert($table_projects, [
                'project_code' => 'PRJ-2026-002',
                'project_name' => 'Metro Plaza Commercial Mall',
                'client_name' => 'Metro Infra Developers',
                'project_type' => 'Commercial Projects',
                'location' => 'S.G. Highway, Ahmedabad, Gujarat',
                'start_date' => '2026-03-01',
                'end_date' => '2028-06-30',
                'estimated_cost' => 120000000.00,
                'actual_cost' => 0.00,
                'project_manager' => 'Construction PM',
                'status' => 'Planning'
            ]);

            // 2. Seed Milestones
            $table_milestones = $wpdb->prefix . 'construction_milestones';
            $wpdb->insert($table_milestones, [
                'project_id' => $proj1_id,
                'milestone_name' => 'Foundation & Excavation Completion',
                'planned_date' => '2026-04-15',
                'actual_date' => '2026-04-20',
                'completion_percentage' => 100.00,
                'status' => 'Completed'
            ]);
            $wpdb->insert($table_milestones, [
                'project_id' => $proj1_id,
                'milestone_name' => 'Ground Floor Slab Pouring',
                'planned_date' => '2026-08-30',
                'actual_date' => null,
                'completion_percentage' => 45.00,
                'status' => 'In-Progress'
            ]);

            // 3. Seed Suppliers
            $table_suppliers = $wpdb->prefix . 'construction_suppliers';
            $wpdb->insert($table_suppliers, [
                'supplier_name' => 'UltraTech Cement Distributors',
                'contact_person' => 'Sanjay Mehta',
                'mobile' => '9898012345',
                'email' => 'sanjay@ultratechdist.com',
                'gst_number' => '24AAAUC1234A1Z1',
                'address' => 'GIDC Industrial Estate, Vadodara, Gujarat',
                'rating' => 4.80,
                'status' => 'ACTIVE'
            ]);
            $sup1_id = $wpdb->insert_id;

            $wpdb->insert($table_suppliers, [
                'supplier_name' => 'Tata Tiscon Steel Yards',
                'contact_person' => 'Rajesh Gupta',
                'mobile' => '9876501234',
                'email' => 'sales@tatatisconyards.com',
                'gst_number' => '24AAACT8876B2Z9',
                'address' => 'Kalamboli Steel Market, Navi Mumbai, MH',
                'rating' => 4.50,
                'status' => 'ACTIVE'
            ]);
            $sup2_id = $wpdb->insert_id;

            // 4. Seed Materials Inventory
            $table_materials = $wpdb->prefix . 'construction_materials';
            $wpdb->insert($table_materials, [
                'material_code' => 'MAT-CEM-OPC',
                'material_name' => 'UltraTech OPC 53 Grade Cement',
                'unit' => 'Bags',
                'available_quantity' => 1500.00,
                'minimum_stock' => 500.00,
                'purchase_price' => 420.00,
                'supplier_id' => $sup1_id,
                'status' => 'ACTIVE'
            ]);
            $mat1_id = $wpdb->insert_id;

            $wpdb->insert($table_materials, [
                'material_code' => 'MAT-STL-12M',
                'material_name' => 'Tata Tiscon TMT Steel Rebars 12mm',
                'unit' => 'Tonnes',
                'available_quantity' => 25.00,
                'minimum_stock' => 5.00,
                'purchase_price' => 62000.00,
                'supplier_id' => $sup2_id,
                'status' => 'ACTIVE'
            ]);
            $mat2_id = $wpdb->insert_id;

            // 5. Seed Purchases
            $table_purchases = $wpdb->prefix . 'construction_purchases';
            $wpdb->insert($table_purchases, [
                'purchase_order_number' => 'PO-2026-0001',
                'project_id' => $proj1_id,
                'supplier_id' => $sup1_id,
                'material_id' => $mat1_id,
                'quantity' => 1000.00,
                'rate' => 410.00,
                'gst_amount' => 73800.00, -- 18% GST
                'total_amount' => 483800.00,
                'purchase_date' => '2026-05-10',
                'status' => 'Approved'
            ]);

            // 6. Seed Site Expenses
            $table_expenses = $wpdb->prefix . 'construction_site_expenses';
            $wpdb->insert($table_expenses, [
                'project_id' => $proj1_id,
                'expense_type' => 'Equipment Rent',
                'amount' => 120000.00,
                'expense_date' => '2026-06-01',
                'description' => 'Monthly rental for JCB excavator & Concrete mixer truck',
                'approved_by' => 'Construction Super Admin'
            ]);
            $wpdb->insert($table_expenses, [
                'project_id' => $proj1_id,
                'expense_type' => 'Fuel',
                'amount' => 35000.00,
                'expense_date' => '2026-06-14',
                'description' => 'Diesel fuel purchase for backup generators & cranes',
                'approved_by' => 'Construction PM'
            ]);

            // 7. Seed Contractors
            $table_contractors = $wpdb->prefix . 'construction_contractors';
            $wpdb->insert($table_contractors, [
                'contractor_code' => 'CON-CIV-001',
                'contractor_name' => 'Rajasthan Civil Builders & Shuttering',
                'mobile' => '9922334455',
                'email' => 'builders@rajasthancivil.com',
                'address' => 'MI Road, Jaipur, Rajasthan',
                'specialization' => 'Civil Work',
                'contract_value' => 8500000.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_contractors, [
                'contractor_code' => 'CON-ELC-002',
                'contractor_name' => 'Bajaj Electrical Systems',
                'mobile' => '9988776655',
                'email' => 'projects@bajajelectricals.erp',
                'address' => 'Worli, Mumbai, Maharashtra',
                'specialization' => 'Electrical Work',
                'contract_value' => 3200000.00,
                'status' => 'ACTIVE'
            ]);

            // 8. Seed Labours
            $table_labours = $wpdb->prefix . 'construction_labours';
            $wpdb->insert($table_labours, [
                'employee_code' => 'LAB-2026-0001',
                'name' => 'Ramu Yadav',
                'mobile' => '9000123456',
                'trade' => 'Mason',
                'daily_wage' => 650.00,
                'attendance_status' => 'PRESENT',
                'project_id' => $proj1_id
            ]);
            $lab1_id = $wpdb->insert_id;

            $wpdb->insert($table_labours, [
                'employee_code' => 'LAB-2026-0002',
                'name' => 'Shyam Lal',
                'mobile' => '9000123457',
                'trade' => 'Helper',
                'daily_wage' => 450.00,
                'attendance_status' => 'PRESENT',
                'project_id' => $proj1_id
            ]);
            $lab2_id = $wpdb->insert_id;

            // 9. Seed Attendance
            $table_attendance = $wpdb->prefix . 'construction_attendance';
            $wpdb->insert($table_attendance, [
                'labour_id' => $lab1_id,
                'project_id' => $proj1_id,
                'attendance_date' => current_time('Y-m-d'),
                'status' => 'Present',
                'working_hours' => 8.00,
                'overtime_hours' => 2.00
            ]);
            $wpdb->insert($table_attendance, [
                'labour_id' => $lab2_id,
                'project_id' => $proj1_id,
                'attendance_date' => current_time('Y-m-d'),
                'status' => 'Present',
                'working_hours' => 8.00,
                'overtime_hours' => 0.00
            ]);

            // 10. Seed Payroll
            $table_payroll = $wpdb->prefix . 'construction_payroll';
            $wpdb->insert($table_payroll, [
                'labour_id' => $lab1_id,
                'project_id' => $proj1_id,
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-07',
                'total_days_worked' => 6,
                'regular_earnings' => 3900.00,
                'overtime_earnings' => 487.50, // 1.5x hourly wage overtime
                'total_earnings' => 4387.50,
                'payment_status' => 'Paid',
                'payment_date' => '2026-06-08'
            ]);

            // 11. Seed Progress Tracking
            $table_progress = $wpdb->prefix . 'construction_progress';
            $wpdb->insert($table_progress, [
                'project_id' => $proj1_id,
                'work_category' => 'Foundation',
                'planned_percentage' => 100.00,
                'actual_percentage' => 100.00,
                'remarks' => 'Completed full structural foundation & sub-surface waterproofing works.',
                'photos' => 'https://domain.com/wp-content/uploads/construction/foundation_1.jpg',
                'update_date' => '2026-04-20'
            ]);
            $wpdb->insert($table_progress, [
                'project_id' => $proj1_id,
                'work_category' => 'Brickwork',
                'planned_percentage' => 35.00,
                'actual_percentage' => 28.00,
                'remarks' => 'Slight delays in brick deliveries due to local monsoon rains. Catching up in the coming week.',
                'photos' => 'https://domain.com/wp-content/uploads/construction/brickwork_1.jpg',
                'update_date' => current_time('Y-m-d')
            ]);

            // 12. Seed Equipment
            $table_equipment = $wpdb->prefix . 'construction_equipment';
            $wpdb->insert($table_equipment, [
                'equipment_code' => 'EQP-JCB-04',
                'equipment_name' => 'JCB 3DX Super Backhoe Excavator',
                'purchase_cost' => 3200000.00,
                'rental_cost' => 3500.00,
                'location' => 'Marwari Heights Site',
                'maintenance_due' => '2026-09-15',
                'status' => 'In-Use'
            ]);
            $wpdb->insert($table_equipment, [
                'equipment_code' => 'EQP-MIX-01',
                'equipment_name' => 'Schwing Stetter Transit Concrete Mixer',
                'purchase_cost' => 4500000.00,
                'rental_cost' => 4800.00,
                'location' => 'Tata Yards',
                'maintenance_due' => '2026-08-01',
                'status' => 'Available'
            ]);

            // 13. Seed Billing
            $table_billing = $wpdb->prefix . 'construction_billing';
            $wpdb->insert($table_billing, [
                'invoice_number' => 'INV-2026-0001',
                'project_id' => $proj1_id,
                'client_name' => 'Marwari Realties Pvt Ltd',
                'milestone_name' => 'Foundation Completion',
                'invoice_amount' => 10000000.00,
                'gst_amount' => 1800000.00, -- 18% GST
                'payment_status' => 'PAID',
                'invoice_date' => '2026-04-25'
            ]);
        }
    }
}
