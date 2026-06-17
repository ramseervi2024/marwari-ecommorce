<?php
namespace HrManagementApi\Repositories;

class SalaryRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('salaries', false);
    }

    /**
     * Find salary profile setup of an employee
     */
    public function findByEmployeeId(int $employee_id): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE employee_id = %d";
        $row = $wpdb->get_row($wpdb->prepare($query, $employee_id), ARRAY_A);
        return $row ?: null;
    }
}
