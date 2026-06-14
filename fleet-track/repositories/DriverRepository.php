<?php
namespace FleetTrackPro\Repositories;

class DriverRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('drivers');
    }

    /**
     * Check if a license number already exists
     */
    public function existsLicense(string $license_number, ?int $exclude_id = null): bool {
        global $wpdb;
        if ($exclude_id !== null) {
            $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE LOWER(license_number) = LOWER(%s) AND id != %d AND deleted_at IS NULL";
            return (int)$wpdb->get_var($wpdb->prepare($query, $license_number, $exclude_id)) > 0;
        } else {
            $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE LOWER(license_number) = LOWER(%s) AND deleted_at IS NULL";
            return (int)$wpdb->get_var($wpdb->prepare($query, $license_number)) > 0;
        }
    }

    /**
     * Check if email exists
     */
    public function existsEmail(string $email, ?int $exclude_id = null): bool {
        global $wpdb;
        if ($exclude_id !== null) {
            $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE LOWER(email) = LOWER(%s) AND id != %d AND deleted_at IS NULL";
            return (int)$wpdb->get_var($wpdb->prepare($query, $email, $exclude_id)) > 0;
        } else {
            $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE LOWER(email) = LOWER(%s) AND deleted_at IS NULL";
            return (int)$wpdb->get_var($wpdb->prepare($query, $email)) > 0;
        }
    }

    /**
     * Check if phone exists
     */
    public function existsPhone(string $phone, ?int $exclude_id = null): bool {
        global $wpdb;
        if ($exclude_id !== null) {
            $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE phone = %s AND id != %d AND deleted_at IS NULL";
            return (int)$wpdb->get_var($wpdb->prepare($query, $phone, $exclude_id)) > 0;
        } else {
            $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE phone = %s AND deleted_at IS NULL";
            return (int)$wpdb->get_var($wpdb->prepare($query, $phone)) > 0;
        }
    }
}
