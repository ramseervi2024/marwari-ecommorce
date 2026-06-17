<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class SalaryRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('salaries');
    }

    public function existsSalaryMonth(int $driver_id, string $salary_month, ?int $exclude_id = null): bool {
        global $wpdb;
        $query = "SELECT COUNT(*) FROM {$this->table_name} WHERE driver_id = %d AND salary_month = %s AND deleted_at IS NULL";
        $args = [$driver_id, $salary_month];
        if ($exclude_id !== null) {
            $query .= " AND id != %d";
            $args[] = $exclude_id;
        }
        return (int)$wpdb->get_var($wpdb->prepare($query, $args)) > 0;
    }
}
