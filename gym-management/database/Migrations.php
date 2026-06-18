<?php
namespace GymErpApi\Database;

if (!defined('ABSPATH')) exit;

class Migrations {
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $p = $wpdb->prefix;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sqls = [
            // 1. Plans (Memberships)
            "CREATE TABLE {$p}gym_plans (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                duration_days int(11) NOT NULL DEFAULT 30,
                price decimal(10,2) NOT NULL DEFAULT 0.00,
                description text,
                is_active tinyint(1) DEFAULT 1,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 2. Members
            "CREATE TABLE {$p}gym_members (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                member_id varchar(50) NOT NULL,
                name varchar(150) NOT NULL,
                mobile varchar(20),
                email varchar(100),
                dob date,
                gender varchar(20),
                blood_group varchar(10),
                address text,
                emergency_contact_name varchar(100),
                emergency_contact_number varchar(20),
                join_date date,
                height_cm decimal(5,2),
                weight_kg decimal(5,2),
                medical_history text,
                status varchar(20) DEFAULT 'Active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY member_id (member_id)
            ) $charset_collate;",

            // 3. Trainers
            "CREATE TABLE {$p}gym_trainers (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                name varchar(150) NOT NULL,
                mobile varchar(20),
                email varchar(100),
                specialization varchar(100),
                salary decimal(10,2) DEFAULT 0.00,
                join_date date,
                status varchar(20) DEFAULT 'Active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 4. Memberships (Linking Members to Plans)
            "CREATE TABLE {$p}gym_memberships (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                member_id bigint(20) unsigned NOT NULL,
                plan_id bigint(20) unsigned NOT NULL,
                trainer_id bigint(20) unsigned NULL,
                start_date date NOT NULL,
                end_date date NOT NULL,
                status varchar(20) DEFAULT 'Active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 5. Payments
            "CREATE TABLE {$p}gym_payments (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                invoice_number varchar(50) NOT NULL,
                member_id bigint(20) unsigned NOT NULL,
                membership_id bigint(20) unsigned NULL,
                amount decimal(10,2) NOT NULL DEFAULT 0.00,
                payment_date date NOT NULL,
                payment_mode varchar(50) DEFAULT 'Cash',
                status varchar(20) DEFAULT 'Paid',
                notes text,
                collected_by bigint(20) unsigned,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 6. Diet Plans
            "CREATE TABLE {$p}gym_diet_plans (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                member_id bigint(20) unsigned NOT NULL,
                trainer_id bigint(20) unsigned NULL,
                plan_details text NOT NULL,
                assigned_date date NOT NULL,
                notes text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 7. Attendance
            "CREATE TABLE {$p}gym_attendance (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_type varchar(20) NOT NULL DEFAULT 'Member', -- 'Member' or 'Trainer'
                reference_id bigint(20) unsigned NOT NULL,
                check_in datetime NOT NULL,
                check_out datetime NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;",

            // 8. Activity Logs
            "CREATE TABLE {$p}gym_activity_logs (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                action varchar(100) NOT NULL,
                details text,
                ip_address varchar(50),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;"
        ];

        foreach ($sqls as $sql) { dbDelta($sql); }

        self::seedRoles();
        self::seedData();
    }

    private static function seedRoles() {
        add_role('gym_admin', 'Gym Admin', ['read' => true]);
        add_role('gym_staff', 'Gym Staff', ['read' => true]);
        add_role('gym_trainer', 'Gym Trainer', ['read' => true]);
    }

    private static function seedData() {
        global $wpdb;
        $p = $wpdb->prefix;

        // Create Users
        $users = [
            ['gymadmin', 'admin@gym.local', '123456', 'gym_admin', 'Gym Admin'],
            ['gymstaff', 'staff@gym.local', '123456', 'gym_staff', 'Gym Staff']
        ];
        foreach ($users as $u) {
            if (!username_exists($u[0])) {
                $uid = wp_create_user($u[0], $u[2], $u[1]);
                $user = new \WP_User($uid);
                $user->set_role($u[3]);
                wp_update_user(['ID' => $uid, 'display_name' => $u[4]]);
                update_user_meta($uid, 'gym_user_status', 'APPROVED');
            }
        }

        // Seed Plans
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$p}gym_plans");
        if ($count == 0) {
            $wpdb->insert($p . 'gym_plans', ['name' => '1 Month Monthly Plan', 'duration_days' => 30, 'price' => 1000]);
            $wpdb->insert($p . 'gym_plans', ['name' => '3 Months Quarterly Plan', 'duration_days' => 90, 'price' => 2500]);
            $wpdb->insert($p . 'gym_plans', ['name' => '6 Months Half-Yearly Plan', 'duration_days' => 180, 'price' => 4500]);
            $wpdb->insert($p . 'gym_plans', ['name' => '1 Year Annual Plan', 'duration_days' => 365, 'price' => 8000]);
        }
    }
}
