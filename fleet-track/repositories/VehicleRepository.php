<?php
namespace FleetTrackPro\Repositories;

class VehicleRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('vehicles');
    }

    /**
     * Check if a vehicle number already exists
     */
    public function exists(string $vehicle_number, ?int $exclude_id = null): bool {
        global $wpdb;
        if ($exclude_id !== null) {
            $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE LOWER(vehicle_number) = LOWER(%s) AND id != %d AND deleted_at IS NULL";
            return (int)$wpdb->get_var($wpdb->prepare($query, $vehicle_number, $exclude_id)) > 0;
        } else {
            $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE LOWER(vehicle_number) = LOWER(%s) AND deleted_at IS NULL";
            return (int)$wpdb->get_var($wpdb->prepare($query, $vehicle_number)) > 0;
        }
    }
}
