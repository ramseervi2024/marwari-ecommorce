<?php
namespace HrManagementApi\Repositories;

class AttendanceRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('attendance', false);
    }

    /**
     * Find attendance by employee ID and date
     */
    public function findByEmployeeIdAndDate(int $employee_id, string $date): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE employee_id = %d AND date = %s";
        $row = $wpdb->get_row($wpdb->prepare($query, $employee_id, $date), ARRAY_A);
        return $row ?: null;
    }
}
