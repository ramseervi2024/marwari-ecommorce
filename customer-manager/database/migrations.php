<?php
namespace CustomerManager\Database;

class Migrations {
    
    /**
     * Run migrations and setup initial database tables and roles.
     */
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customers';
        $charset_collate = $wpdb->get_charset_collate();

        // SQL structure for wp_customers table
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) NOT NULL,
            address text DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            state varchar(100) DEFAULT NULL,
            country varchar(100) DEFAULT NULL,
            postal_code varchar(20) DEFAULT NULL,
            status varchar(20) DEFAULT 'ACTIVE' NOT NULL,
            is_deleted tinyint(1) DEFAULT 0 NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email),
            UNIQUE KEY phone (phone),
            KEY is_deleted (is_deleted)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Setup custom WordPress Roles for API
        self::setupRoles();

        // Generate and save a secure JWT secret key if not already defined
        if (!get_option('customer_manager_jwt_secret')) {
            $secret = bin2hex(random_bytes(32));
            update_option('customer_manager_jwt_secret', $secret);
        }
    }

    /**
     * Setup user roles and capabilities.
     */
    private static function setupRoles() {
        // Super Admin capabilities
        $super_admin_caps = [
            'read' => true,
            'create_customers' => true,
            'edit_customers' => true,
            'delete_customers' => true,
            'view_customers' => true,
            'export_customers' => true,
            'import_customers' => true,
            'access_dashboard' => true,
        ];

        // Manager capabilities
        $manager_caps = [
            'read' => true,
            'create_customers' => true,
            'edit_customers' => true,
            'view_customers' => true,
        ];

        // Viewer capabilities
        $viewer_caps = [
            'read' => true,
            'view_customers' => true,
        ];

        // Register WordPress Roles
        remove_role('api_super_admin');
        remove_role('api_manager');
        remove_role('api_viewer');

        add_role('api_super_admin', 'API Super Admin', $super_admin_caps);
        add_role('api_manager', 'API Manager', $manager_caps);
        add_role('api_viewer', 'API Viewer', $viewer_caps);
    }

    /**
     * Clean up custom user roles on plugin deactivation.
     */
    public static function deactivate() {
        remove_role('api_super_admin');
        remove_role('api_manager');
        remove_role('api_viewer');
    }
}
