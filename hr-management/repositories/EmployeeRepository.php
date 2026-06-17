<?php
namespace HrManagementApi\Repositories;

class EmployeeRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('employees', true);
    }

    /**
     * Find employee metadata by WordPress User ID
     */
    public function findByUserId(int $user_id): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE user_id = %d AND deleted_at IS NULL";
        $row = $wpdb->get_row($wpdb->prepare($query, $user_id), ARRAY_A);
        return $row ?: null;
    }
}
