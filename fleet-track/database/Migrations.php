<?php
namespace FleetTrackPro\Database;

class Migrations {
    
    /**
     * Activate migrations - Creates tables & user roles
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // 1. JWT Secret Setup
        if (!get_option('fleet_track_jwt_secret')) {
            update_option('fleet_track_jwt_secret', bin2hex(random_bytes(32)));
        }
        
        // 2. Vehicles Table
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        $sql_vehicles = "CREATE TABLE $table_vehicles (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vehicle_number varchar(50) NOT NULL,
            vehicle_type varchar(50) NOT NULL,
            vehicle_brand varchar(50) NOT NULL,
            vehicle_model varchar(50) NOT NULL,
            vehicle_year int(11) NOT NULL,
            fuel_type varchar(20) NOT NULL,
            capacity varchar(50) NOT NULL,
            insurance_expiry date DEFAULT NULL,
            fitness_expiry date DEFAULT NULL,
            permit_expiry date DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY vehicle_number (vehicle_number)
        ) $charset_collate;";
        dbDelta($sql_vehicles);

        // 3. Drivers Table
        $table_drivers = $wpdb->prefix . 'fleet_drivers';
        $sql_drivers = "CREATE TABLE $table_drivers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) NOT NULL,
            license_number varchar(50) NOT NULL,
            license_expiry date DEFAULT NULL,
            salary decimal(10,2) NOT NULL DEFAULT 0.00,
            joining_date date DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email),
            UNIQUE KEY phone (phone),
            UNIQUE KEY license_number (license_number)
        ) $charset_collate;";
        dbDelta($sql_drivers);

        // 4. Routes Table
        $table_routes = $wpdb->prefix . 'fleet_routes';
        $sql_routes = "CREATE TABLE $table_routes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            route_name varchar(100) NOT NULL,
            source varchar(100) NOT NULL,
            destination varchar(100) NOT NULL,
            distance_km decimal(8,2) NOT NULL DEFAULT 0.00,
            estimated_time varchar(50) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_routes);

        // 5. Trips Table
        $table_trips = $wpdb->prefix . 'fleet_trips';
        $sql_trips = "CREATE TABLE $table_trips (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vehicle_id bigint(20) NOT NULL,
            driver_id bigint(20) NOT NULL,
            route_id bigint(20) NOT NULL,
            trip_date date NOT NULL,
            start_km decimal(10,2) NOT NULL DEFAULT 0.00,
            end_km decimal(10,2) NOT NULL DEFAULT 0.00,
            distance_travelled decimal(10,2) NOT NULL DEFAULT 0.00,
            revenue decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(20) NOT NULL DEFAULT 'PLANNED',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_trips);

        // 6. Expenses Table
        $table_expenses = $wpdb->prefix . 'fleet_expenses';
        $sql_expenses = "CREATE TABLE $table_expenses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vehicle_id bigint(20) DEFAULT NULL,
            driver_id bigint(20) DEFAULT NULL,
            trip_id bigint(20) DEFAULT NULL,
            expense_type varchar(50) NOT NULL,
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            expense_date date NOT NULL,
            description text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_expenses);

        // 7. Fuel Table
        $table_fuel = $wpdb->prefix . 'fleet_fuel';
        $sql_fuel = "CREATE TABLE $table_fuel (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vehicle_id bigint(20) NOT NULL,
            trip_id bigint(20) DEFAULT NULL,
            fuel_quantity decimal(8,2) NOT NULL DEFAULT 0.00,
            fuel_cost decimal(10,2) NOT NULL DEFAULT 0.00,
            fuel_price_per_liter decimal(8,2) NOT NULL DEFAULT 0.00,
            fuel_station varchar(100) DEFAULT NULL,
            fuel_date date NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_fuel);

        // 8. Documents Table
        $table_documents = $wpdb->prefix . 'fleet_documents';
        $sql_documents = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            related_type varchar(20) NOT NULL,
            related_id bigint(20) NOT NULL,
            document_type varchar(50) NOT NULL,
            file_url varchar(255) NOT NULL,
            media_id bigint(20) NOT NULL,
            expiry_date date DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_documents);

        // 9. Activity Logs Table
        $table_logs = $wpdb->prefix . 'fleet_activity_logs';
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

        // 10. Register Custom Roles and Capabilities
        self::register_roles();
    }
    
    /**
     * Set up user roles in WordPress and capability levels
     */
    private static function register_roles() {
        // Remove existing roles if any to start clean (optional, but clean for upgrades)
        remove_role('fleet_super_admin');
        remove_role('fleet_manager');
        remove_role('fleet_accountant');
        remove_role('fleet_driver');
        
        $super_admin_caps = [
            'read' => true,
            'manage_fleet' => true,
            'view_fleet' => true,
            'manage_fleet_users' => true,
            'manage_vehicles' => true,
            'view_vehicles' => true,
            'manage_drivers' => true,
            'view_drivers' => true,
            'manage_routes' => true,
            'view_routes' => true,
            'manage_trips' => true,
            'view_trips' => true,
            'update_trip_status' => true,
            'manage_expenses' => true,
            'view_expenses' => true,
            'view_reports' => true,
            'upload_documents' => true,
        ];
        
        $manager_caps = [
            'read' => true,
            'view_fleet' => true,
            'manage_vehicles' => true,
            'view_vehicles' => true,
            'manage_drivers' => true,
            'view_drivers' => true,
            'manage_routes' => true,
            'view_routes' => true,
            'manage_trips' => true,
            'view_trips' => true,
            'update_trip_status' => true,
            'manage_expenses' => true,
            'view_expenses' => true,
            'view_reports' => true,
            'upload_documents' => true,
        ];
        
        $accountant_caps = [
            'read' => true,
            'view_fleet' => true,
            'view_vehicles' => true,
            'view_drivers' => true,
            'view_routes' => true,
            'view_trips' => true,
            'manage_expenses' => true,
            'view_expenses' => true,
            'view_reports' => true,
            'upload_documents' => true,
        ];
        
        $driver_caps = [
            'read' => true,
            'view_trips' => true,
            'update_trip_status' => true,
            'upload_documents' => true,
        ];
        
        add_role('fleet_super_admin', 'Fleet Super Admin', $super_admin_caps);
        add_role('fleet_manager', 'Fleet Manager', $manager_caps);
        add_role('fleet_accountant', 'Fleet Accountant', $accountant_caps);
        add_role('fleet_driver', 'Fleet Driver', $driver_caps);

        // Ensure Administrator has all permissions
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($super_admin_caps as $cap => $grant) {
                $admin_role->add_cap($cap);
            }
        }
    }
}
