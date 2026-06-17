<?php
namespace HrManagementApi\Database;

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

        // 1. Employees Metadata Profile
        $table_employees = $wpdb->prefix . 'hr_employees';
        $tables[] = "CREATE TABLE $table_employees (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            department varchar(100) DEFAULT '',
            designation varchar(100) DEFAULT '',
            date_of_joining date DEFAULT NULL,
            pf_number varchar(50) DEFAULT '',
            esi_number varchar(50) DEFAULT '',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;";

        // 2. Daily Attendance Logs
        $table_attendance = $wpdb->prefix . 'hr_attendance';
        $tables[] = "CREATE TABLE $table_attendance (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_id bigint(20) NOT NULL,
            date date NOT NULL,
            check_in time DEFAULT NULL,
            check_out time DEFAULT NULL,
            total_hours decimal(5,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Present',
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY emp_date (employee_id, date)
        ) $charset_collate;";

        // 3. Leave Requests
        $table_leaves = $wpdb->prefix . 'hr_leaves';
        $tables[] = "CREATE TABLE $table_leaves (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_id bigint(20) NOT NULL,
            leave_type varchar(50) NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            reason text DEFAULT NULL,
            status varchar(50) DEFAULT 'Pending',
            approved_by bigint(20) DEFAULT NULL,
            comments text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 4. Leave Balances
        $table_leave_balances = $wpdb->prefix . 'hr_leave_balances';
        $tables[] = "CREATE TABLE $table_leave_balances (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_id bigint(20) NOT NULL,
            casual_leaves int(11) DEFAULT '12',
            medical_leaves int(11) DEFAULT '10',
            earned_leaves int(11) DEFAULT '15',
            unpaid_leaves int(11) DEFAULT '0',
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY emp_id (employee_id)
        ) $charset_collate;";

        // 5. Salary Setup Profiles
        $table_salaries = $wpdb->prefix . 'hr_salaries';
        $tables[] = "CREATE TABLE $table_salaries (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_id bigint(20) NOT NULL,
            base_salary decimal(10,2) DEFAULT '0.00',
            allowances decimal(10,2) DEFAULT '0.00',
            deductions decimal(10,2) DEFAULT '0.00',
            pf_contribution decimal(10,2) DEFAULT '0.00',
            esi_contribution decimal(10,2) DEFAULT '0.00',
            net_salary decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY emp_sal (employee_id)
        ) $charset_collate;";

        // 6. Monthly Payroll Payslips
        $table_payslips = $wpdb->prefix . 'hr_payslips';
        $tables[] = "CREATE TABLE $table_payslips (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_id bigint(20) NOT NULL,
            month varchar(20) NOT NULL,
            year int(11) NOT NULL,
            base_salary decimal(10,2) DEFAULT '0.00',
            allowances decimal(10,2) DEFAULT '0.00',
            deductions decimal(10,2) DEFAULT '0.00',
            pf_deduction decimal(10,2) DEFAULT '0.00',
            esi_deduction decimal(10,2) DEFAULT '0.00',
            net_salary decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Generated',
            generated_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY emp_month_year (employee_id, month, year)
        ) $charset_collate;";

        // 7. Employee Documents registry
        $table_documents = $wpdb->prefix . 'hr_documents';
        $tables[] = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_id bigint(20) NOT NULL,
            document_name varchar(150) NOT NULL,
            document_type varchar(50) DEFAULT 'ID Proof',
            file_url varchar(255) DEFAULT '',
            status varchar(50) DEFAULT 'Active',
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 8. Logs
        $table_logs = $wpdb->prefix . 'hr_activity_logs';
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
        remove_role('hr_super_admin');
        remove_role('hr_manager');
        remove_role('hr_accountant');
        remove_role('hr_employee');

        // Super Admin capabilities
        $super_admin_caps = [
            'read' => true,
            'manage_employees' => true,
            'manage_attendance' => true,
            'manage_leaves' => true,
            'manage_payroll' => true,
            'manage_documents' => true,
            'manage_hr_users' => true,
            'view_hr_dashboard' => true,
            'view_hr_reports' => true,
            'view_own_payroll' => true,
            'manage_own_attendance' => true,
            'manage_own_leaves' => true,
            'manage_own_documents' => true
        ];

        // Manager capabilities
        $manager_caps = [
            'read' => true,
            'view_hr_dashboard' => true,
            'manage_attendance' => true,
            'manage_leaves' => true,
            'manage_documents' => true,
            'view_hr_reports' => true,
            'view_own_payroll' => true,
            'manage_own_attendance' => true,
            'manage_own_leaves' => true,
            'manage_own_documents' => true
        ];

        // Accountant capabilities
        $accountant_caps = [
            'read' => true,
            'view_hr_dashboard' => true,
            'manage_payroll' => true,
            'view_hr_reports' => true,
            'view_own_payroll' => true,
            'manage_own_attendance' => true,
            'manage_own_leaves' => true,
            'manage_own_documents' => true
        ];

        // Employee capabilities
        $employee_caps = [
            'read' => true,
            'view_hr_dashboard' => true,
            'view_own_payroll' => true,
            'manage_own_attendance' => true,
            'manage_own_leaves' => true,
            'manage_own_documents' => true
        ];

        add_role('hr_super_admin', 'HR Super Admin', $super_admin_caps);
        add_role('hr_manager', 'HR Manager', $manager_caps);
        add_role('hr_accountant', 'HR Accountant', $accountant_caps);
        add_role('hr_employee', 'HR Employee', $employee_caps);

        // Map capabilities to admin role
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
            'hsuperadmin' => ['role' => 'hr_super_admin', 'name' => 'HR Super Admin'],
            'hmanager' => ['role' => 'hr_manager', 'name' => 'Jane HR Manager'],
            'haccountant' => ['role' => 'hr_accountant', 'name' => 'Suresh Payroll Accountant'],
            'hemployee' => ['role' => 'hr_employee', 'name' => 'Rahul Employee']
        ];

        $employee_map = [];

        foreach ($users_data as $username => $info) {
            if (!username_exists($username)) {
                $user_id = wp_create_user($username, '123456', $username . '@hr-erp.com');
                if (!is_wp_error($user_id)) {
                    $user = new \WP_User($user_id);
                    $user->set_role($info['role']);
                    wp_update_user([
                        'ID' => $user_id,
                        'display_name' => $info['name'],
                        'first_name' => explode(' ', $info['name'])[0]
                    ]);
                    update_user_meta($user_id, 'hr_user_status', 'APPROVED');
                    $employee_map[$info['role']] = $user_id;
                }
            } else {
                $user = get_user_by('login', $username);
                $employee_map[$info['role']] = $user->ID;
            }
        }

        // Seed extended employee records
        $table_employees = $wpdb->prefix . 'hr_employees';
        $table_leave_balances = $wpdb->prefix . 'hr_leave_balances';
        $table_salaries = $wpdb->prefix . 'hr_salaries';

        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_employees")) === 0) {
            foreach ($employee_map as $role => $user_id) {
                $dept = 'Administration';
                $desg = 'HR Super Admin';
                if ($role === 'hr_manager') {
                    $dept = 'Human Resources';
                    $desg = 'HR General Manager';
                } elseif ($role === 'hr_accountant') {
                    $dept = 'Finance & Accounts';
                    $desg = 'Senior Accountant';
                } elseif ($role === 'hr_employee') {
                    $dept = 'Engineering';
                    $desg = 'Junior Software Engineer';
                }

                // Insert Employee profile
                $wpdb->insert($table_employees, [
                    'user_id' => $user_id,
                    'department' => $dept,
                    'designation' => $desg,
                    'date_of_joining' => '2025-01-15',
                    'pf_number' => 'MH/BAN/0012345/000/0123',
                    'esi_number' => '31001234560011001',
                    'status' => 'ACTIVE'
                ]);
                $emp_id = $wpdb->insert_id;

                if ($emp_id) {
                    // Seed Leave Balance
                    $wpdb->insert($table_leave_balances, [
                        'employee_id' => $emp_id,
                        'casual_leaves' => 12,
                        'medical_leaves' => 10,
                        'earned_leaves' => 15,
                        'unpaid_leaves' => 0
                    ]);

                    // Seed Salary Setup
                    $base = 25000.00;
                    $allowances = 5000.00;
                    $deductions = 1000.00;
                    
                    if ($role === 'hr_manager') {
                        $base = 45000.00;
                        $allowances = 8000.00;
                    } elseif ($role === 'hr_accountant') {
                        $base = 35000.00;
                        $allowances = 6000.00;
                    }
                    
                    // PF is 12% of base salary, ESI is 0.75% of gross
                    $gross = $base + $allowances;
                    $pf = round($base * 0.12, 2);
                    $esi = round($gross * 0.0075, 2);
                    $net = $gross - ($deductions + $pf + $esi);

                    $wpdb->insert($table_salaries, [
                        'employee_id' => $emp_id,
                        'base_salary' => $base,
                        'allowances' => $allowances,
                        'deductions' => $deductions,
                        'pf_contribution' => $pf,
                        'esi_contribution' => $esi,
                        'net_salary' => $net,
                        'status' => 'Active'
                    ]);
                }
            }
        }
    }
}
