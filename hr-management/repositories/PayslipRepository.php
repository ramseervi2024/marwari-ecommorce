<?php
namespace HrManagementApi\Repositories;

class PayslipRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('payslips', false);
    }

    /**
     * Find monthly payslip for an employee
     */
    public function findByEmployeeIdMonthAndYear(int $employee_id, string $month, int $year): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE employee_id = %d AND month = %s AND year = %d";
        $row = $wpdb->get_row($wpdb->prepare($query, $employee_id, $month, $year), ARRAY_A);
        return $row ?: null;
    }
}
