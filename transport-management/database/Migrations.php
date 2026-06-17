<?php
namespace TransportManagementApi\Database;

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

        // 1. Vehicles table
        $table_vehicles = $wpdb->prefix . 'transport_vehicles';
        $tables[] = "CREATE TABLE $table_vehicles (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vehicle_number varchar(50) NOT NULL,
            vehicle_type varchar(50) NOT NULL,
            vehicle_model varchar(100) DEFAULT '',
            registration_number varchar(100) DEFAULT '',
            insurance_expiry date DEFAULT NULL,
            permit_expiry date DEFAULT NULL,
            fitness_expiry date DEFAULT NULL,
            purchase_date date DEFAULT NULL,
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY vehicle_number (vehicle_number)
        ) $charset_collate;";

        // 2. Drivers table
        $table_drivers = $wpdb->prefix . 'transport_drivers';
        $tables[] = "CREATE TABLE $table_drivers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            driver_code varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            mobile varchar(20) NOT NULL,
            license_number varchar(100) NOT NULL,
            license_expiry date DEFAULT NULL,
            joining_date date DEFAULT NULL,
            salary_type varchar(50) DEFAULT 'fixed',
            fixed_salary decimal(10,2) DEFAULT '0.00',
            per_trip_salary decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY driver_code (driver_code)
        ) $charset_collate;";

        // 3. Routes table
        $table_routes = $wpdb->prefix . 'transport_routes';
        $tables[] = "CREATE TABLE $table_routes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            route_code varchar(50) NOT NULL,
            source varchar(100) NOT NULL,
            destination varchar(100) NOT NULL,
            distance_km int(11) DEFAULT '0',
            estimated_time varchar(50) DEFAULT '',
            toll_charges decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'ACTIVE',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY route_code (route_code)
        ) $charset_collate;";

        // 4. Trips table
        $table_trips = $wpdb->prefix . 'transport_trips';
        $tables[] = "CREATE TABLE $table_trips (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            trip_number varchar(50) NOT NULL,
            vehicle_id bigint(20) NOT NULL,
            driver_id bigint(20) NOT NULL,
            route_id bigint(20) NOT NULL,
            customer_name varchar(100) DEFAULT '',
            loading_point varchar(100) DEFAULT '',
            unloading_point varchar(100) DEFAULT '',
            trip_start_date date DEFAULT NULL,
            trip_end_date date DEFAULT NULL,
            freight_amount decimal(10,2) DEFAULT '0.00',
            status varchar(50) DEFAULT 'Assigned',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY trip_number (trip_number)
        ) $charset_collate;";

        // 5. Deliveries table
        $table_deliveries = $wpdb->prefix . 'transport_deliveries';
        $tables[] = "CREATE TABLE $table_deliveries (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            trip_id bigint(20) NOT NULL,
            tracking_number varchar(50) NOT NULL,
            customer_name varchar(100) DEFAULT '',
            delivery_address text DEFAULT NULL,
            delivery_status varchar(50) DEFAULT 'Picked Up',
            latitude varchar(50) DEFAULT '',
            longitude varchar(50) DEFAULT '',
            proof_of_delivery varchar(255) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY tracking_number (tracking_number)
        ) $charset_collate;";

        // 6. Fuel tracking table
        $table_fuel = $wpdb->prefix . 'transport_fuel';
        $tables[] = "CREATE TABLE $table_fuel (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vehicle_id bigint(20) NOT NULL,
            trip_id bigint(20) DEFAULT NULL,
            fuel_station varchar(100) DEFAULT '',
            fuel_quantity decimal(10,2) DEFAULT '0.00',
            rate_per_liter decimal(10,2) DEFAULT '0.00',
            total_cost decimal(10,2) DEFAULT '0.00',
            odometer_reading int(11) DEFAULT '0',
            fuel_date date DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 7. Maintenance table
        $table_maintenance = $wpdb->prefix . 'transport_maintenance';
        $tables[] = "CREATE TABLE $table_maintenance (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vehicle_id bigint(20) NOT NULL,
            maintenance_type varchar(100) NOT NULL,
            description text DEFAULT NULL,
            service_center varchar(100) DEFAULT '',
            cost decimal(10,2) DEFAULT '0.00',
            service_date date DEFAULT NULL,
            next_service_date date DEFAULT NULL,
            status varchar(50) DEFAULT 'Scheduled',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 8. Driver Salaries table
        $table_salaries = $wpdb->prefix . 'transport_salaries';
        $tables[] = "CREATE TABLE $table_salaries (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            driver_id bigint(20) NOT NULL,
            salary_month varchar(10) NOT NULL,
            fixed_salary decimal(10,2) DEFAULT '0.00',
            trip_bonus decimal(10,2) DEFAULT '0.00',
            allowance decimal(10,2) DEFAULT '0.00',
            deduction decimal(10,2) DEFAULT '0.00',
            total_salary decimal(10,2) DEFAULT '0.00',
            payment_status varchar(50) DEFAULT 'Pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY driver_month (driver_id, salary_month)
        ) $charset_collate;";

        // 9. Challans table
        $table_challans = $wpdb->prefix . 'transport_challans';
        $tables[] = "CREATE TABLE $table_challans (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vehicle_id bigint(20) NOT NULL,
            driver_id bigint(20) NOT NULL,
            challan_number varchar(100) NOT NULL,
            challan_type varchar(100) NOT NULL,
            challan_amount decimal(10,2) DEFAULT '0.00',
            challan_date date DEFAULT NULL,
            payment_status varchar(50) DEFAULT 'Pending',
            remarks text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY challan_number (challan_number)
        ) $charset_collate;";

        // 10. Expenses table
        $table_expenses = $wpdb->prefix . 'transport_expenses';
        $tables[] = "CREATE TABLE $table_expenses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            trip_id bigint(20) DEFAULT NULL,
            expense_type varchar(100) NOT NULL,
            amount decimal(10,2) DEFAULT '0.00',
            expense_date date DEFAULT NULL,
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 11. Customers table
        $table_customers = $wpdb->prefix . 'transport_customers';
        $tables[] = "CREATE TABLE $table_customers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            customer_code varchar(50) NOT NULL,
            company_name varchar(100) NOT NULL,
            contact_person varchar(100) DEFAULT '',
            mobile varchar(20) DEFAULT '',
            email varchar(100) DEFAULT '',
            address text DEFAULT NULL,
            gst_number varchar(20) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY customer_code (customer_code)
        ) $charset_collate;";

        // 12. Freight Billing (Invoices) table
        $table_billing = $wpdb->prefix . 'transport_billing';
        $tables[] = "CREATE TABLE $table_billing (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_number varchar(50) NOT NULL,
            trip_id bigint(20) NOT NULL,
            customer_id bigint(20) NOT NULL,
            freight_amount decimal(10,2) DEFAULT '0.00',
            fuel_surcharge decimal(10,2) DEFAULT '0.00',
            gst_amount decimal(10,2) DEFAULT '0.00',
            total_amount decimal(10,2) DEFAULT '0.00',
            payment_status varchar(50) DEFAULT 'Unpaid',
            invoice_date date DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY invoice_number (invoice_number)
        ) $charset_collate;";

        // 13. Document Management table
        $table_documents = $wpdb->prefix . 'transport_documents';
        $tables[] = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            entity_type varchar(50) NOT NULL,
            entity_id bigint(20) NOT NULL,
            document_type varchar(50) NOT NULL,
            file_url varchar(255) NOT NULL,
            expiry_date date DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 14. Activity Logs table
        $table_logs = $wpdb->prefix . 'transport_activity_logs';
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
        // Remove existing custom roles if any to re-initialize
        remove_role('transport_super_admin');
        remove_role('transport_fleet_manager');
        remove_role('transport_operations_manager');
        remove_role('transport_driver');
        remove_role('transport_accountant');

        // Super Admin Role
        add_role('transport_super_admin', 'Transport Super Admin', [
            'read' => true,
            'manage_transport' => true,
            'manage_users' => true
        ]);

        // Fleet Manager Role
        add_role('transport_fleet_manager', 'Transport Fleet Manager', [
            'read' => true,
            'manage_vehicles' => true,
            'manage_trips' => true,
            'manage_maintenance' => true,
            'manage_fuel' => true
        ]);

        // Operations Manager Role
        add_role('transport_operations_manager', 'Transport Operations Manager', [
            'read' => true,
            'manage_routes' => true,
            'manage_deliveries' => true
        ]);

        // Driver Role
        add_role('transport_driver', 'Transport Driver', [
            'read' => true,
            'view_assigned_trips' => true,
            'update_delivery_status' => true
        ]);

        // Accountant Role
        add_role('transport_accountant', 'Transport Accountant', [
            'read' => true,
            'manage_salaries' => true,
            'manage_expenses' => true,
            'manage_challans' => true,
            'manage_billing' => true
        ]);
    }

    private static function seedData() {
        global $wpdb;

        // Create initial WordPress Users with appropriate transport roles if they do not exist
        $users_data = [
            'tsuperadmin' => ['role' => 'transport_super_admin', 'name' => 'Transport Admin'],
            'tfleetmgr' => ['role' => 'transport_fleet_manager', 'name' => 'John Fleet Manager'],
            'topsmgr' => ['role' => 'transport_operations_manager', 'name' => 'Sarah Operations Manager'],
            'tdriver1' => ['role' => 'transport_driver', 'name' => 'Amit Driver'],
            'taccountant' => ['role' => 'transport_accountant', 'name' => 'Rajesh Accountant']
        ];

        foreach ($users_data as $username => $info) {
            if (!username_exists($username)) {
                $user_id = wp_create_user($username, '123456', $username . '@transport-erp.com');
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

        // Seed 1: Vehicles
        $table_vehicles = $wpdb->prefix . 'transport_vehicles';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_vehicles")) === 0) {
            $wpdb->insert($table_vehicles, [
                'vehicle_number' => 'MH-12-GQ-4321',
                'vehicle_type' => 'Container',
                'vehicle_model' => 'Tata Prima 4925.S',
                'registration_number' => 'REG-MH-827389',
                'insurance_expiry' => '2027-01-15',
                'permit_expiry' => '2027-05-20',
                'fitness_expiry' => '2026-12-30',
                'purchase_date' => '2023-04-10',
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_vehicles, [
                'vehicle_number' => 'HR-55-AA-1122',
                'vehicle_type' => 'Truck',
                'vehicle_model' => 'Ashok Leyland Ecomet',
                'registration_number' => 'REG-HR-998811',
                'insurance_expiry' => '2026-09-12',
                'permit_expiry' => '2027-02-10',
                'fitness_expiry' => '2026-08-30',
                'purchase_date' => '2024-01-18',
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_vehicles, [
                'vehicle_number' => 'KA-03-MM-7788',
                'vehicle_type' => 'Van',
                'vehicle_model' => 'Mahindra Bolero Pik-Up',
                'registration_number' => 'REG-KA-112233',
                'insurance_expiry' => '2026-11-05',
                'permit_expiry' => '2026-10-30',
                'fitness_expiry' => '2026-11-20',
                'purchase_date' => '2025-03-22',
                'status' => 'IN_SERVICE'
            ]);
        }

        // Seed 2: Drivers
        $table_drivers = $wpdb->prefix . 'transport_drivers';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_drivers")) === 0) {
            $wpdb->insert($table_drivers, [
                'driver_code' => 'DRV-001',
                'name' => 'Amit Kumar',
                'mobile' => '9988776655',
                'license_number' => 'DL-MH12-20150927',
                'license_expiry' => '2030-05-14',
                'joining_date' => '2020-06-01',
                'salary_type' => 'fixed',
                'fixed_salary' => 25000.00,
                'per_trip_salary' => 0.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_drivers, [
                'driver_code' => 'DRV-002',
                'name' => 'Rajesh Patil',
                'mobile' => '9876543210',
                'license_number' => 'DL-GJ01-20188273',
                'license_expiry' => '2028-11-12',
                'joining_date' => '2022-03-15',
                'salary_type' => 'per_trip',
                'fixed_salary' => 0.00,
                'per_trip_salary' => 1500.00,
                'status' => 'ACTIVE'
            ]);
        }

        // Seed 3: Routes
        $table_routes = $wpdb->prefix . 'transport_routes';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_routes")) === 0) {
            $wpdb->insert($table_routes, [
                'route_code' => 'RTE-MUM-PUN',
                'source' => 'Mumbai',
                'destination' => 'Pune',
                'distance_km' => 150,
                'estimated_time' => '3.5 Hours',
                'toll_charges' => 320.00,
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_routes, [
                'route_code' => 'RTE-DEL-JAI',
                'source' => 'Delhi',
                'destination' => 'Jaipur',
                'distance_km' => 270,
                'estimated_time' => '5 Hours',
                'toll_charges' => 540.00,
                'status' => 'ACTIVE'
            ]);
        }

        // Seed 4: Customers
        $table_customers = $wpdb->prefix . 'transport_customers';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_customers")) === 0) {
            $wpdb->insert($table_customers, [
                'customer_code' => 'CUST-001',
                'company_name' => 'Tata Steel Ltd',
                'contact_person' => 'Sanjay Bhatia',
                'mobile' => '9892019283',
                'email' => 'logistics@tatasteel.com',
                'address' => 'Jamshedpur Corporate Office, Jharkhand',
                'gst_number' => '20AAACT1234F1Z5'
            ]);
            $wpdb->insert($table_customers, [
                'customer_code' => 'CUST-002',
                'company_name' => 'Marwari Traders',
                'contact_person' => 'Ramesh Marwari',
                'mobile' => '9320192837',
                'email' => 'sales@marwaritraders.com',
                'address' => 'Sadar Bazar, Delhi',
                'gst_number' => '07AAAAM8829K2Z2'
            ]);
        }

        // Seed 5: Trips
        $table_trips = $wpdb->prefix . 'transport_trips';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_trips")) === 0) {
            $wpdb->insert($table_trips, [
                'trip_number' => 'TRIP-2026-0001',
                'vehicle_id' => 1,
                'driver_id' => 1,
                'route_id' => 1,
                'customer_name' => 'Tata Steel Ltd',
                'loading_point' => 'JNPT Port Mumbai',
                'unloading_point' => 'Chinchwad Depot Pune',
                'trip_start_date' => '2026-06-15',
                'trip_end_date' => '2026-06-16',
                'freight_amount' => 45000.00,
                'status' => 'Delivered'
            ]);
            $wpdb->insert($table_trips, [
                'trip_number' => 'TRIP-2026-0002',
                'vehicle_id' => 2,
                'driver_id' => 2,
                'route_id' => 2,
                'customer_name' => 'Marwari Traders',
                'loading_point' => 'Sanjay Gandhi Transport Nagar Delhi',
                'unloading_point' => 'Industrial Area Jaipur',
                'trip_start_date' => '2026-06-17',
                'trip_end_date' => NULL,
                'freight_amount' => 65000.00,
                'status' => 'In Transit'
            ]);
        }

        // Seed 6: Deliveries
        $table_deliveries = $wpdb->prefix . 'transport_deliveries';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_deliveries")) === 0) {
            $wpdb->insert($table_deliveries, [
                'trip_id' => 1,
                'tracking_number' => 'TRK-9908129',
                'customer_name' => 'Tata Steel Ltd',
                'delivery_address' => 'Plot 42, G-Block MIDC Chinchwad, Pune',
                'delivery_status' => 'Delivered',
                'latitude' => '18.6278',
                'longitude' => '73.8131',
                'proof_of_delivery' => 'https://domain.com/wp-content/uploads/pod-delivered.pdf'
            ]);
            $wpdb->insert($table_deliveries, [
                'trip_id' => 2,
                'tracking_number' => 'TRK-1122883',
                'customer_name' => 'Marwari Traders',
                'delivery_address' => 'Plot 105, RIICO Industrial Area, Mansarovar Jaipur',
                'delivery_status' => 'In Transit',
                'latitude' => '27.2038',
                'longitude' => '75.8012',
                'proof_of_delivery' => ''
            ]);
        }

        // Seed 7: Fuel Tracking
        $table_fuel = $wpdb->prefix . 'transport_fuel';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_fuel")) === 0) {
            $wpdb->insert($table_fuel, [
                'vehicle_id' => 1,
                'trip_id' => 1,
                'fuel_station' => 'HP Pump Expressway',
                'fuel_quantity' => 85.50,
                'rate_per_liter' => 94.20,
                'total_cost' => 8054.10,
                'odometer_reading' => 14520,
                'fuel_date' => '2026-06-15'
            ]);
        }

        // Seed 8: Maintenance
        $table_maintenance = $wpdb->prefix . 'transport_maintenance';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_maintenance")) === 0) {
            $wpdb->insert($table_maintenance, [
                'vehicle_id' => 1,
                'maintenance_type' => 'Oil Change',
                'description' => 'Engine oil filter change & fluid check',
                'service_center' => 'Tata authorized service - Panvel',
                'cost' => 8500.00,
                'service_date' => '2026-05-10',
                'next_service_date' => '2026-09-10',
                'status' => 'Completed'
            ]);
        }

        // Seed 9: Challans
        $table_challans = $wpdb->prefix . 'transport_challans';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_challans")) === 0) {
            $wpdb->insert($table_challans, [
                'vehicle_id' => 1,
                'driver_id' => 1,
                'challan_number' => 'CH-2026-88912',
                'challan_type' => 'Overloading',
                'challan_amount' => 5000.00,
                'challan_date' => '2026-06-16',
                'payment_status' => 'Pending',
                'remarks' => 'Checked at Expressway toll plaza weight station'
            ]);
        }

        // Seed 10: Billing
        $table_billing = $wpdb->prefix . 'transport_billing';
        if (intval($wpdb->get_var("SELECT COUNT(*) FROM $table_billing")) === 0) {
            $wpdb->insert($table_billing, [
                'invoice_number' => 'INV-2026-0001',
                'trip_id' => 1,
                'customer_id' => 1,
                'freight_amount' => 45000.00,
                'fuel_surcharge' => 1500.00,
                'gst_amount' => 8370.00, // 18% of 46500
                'total_amount' => 54870.00,
                'payment_status' => 'Paid',
                'invoice_date' => '2026-06-16'
            ]);
        }
    }
}
