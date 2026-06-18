<?php
namespace CrmManagementApi\Repositories;

class DealRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('deals', false);
    }

    public function findByDealNumber(string $deal_number): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE deal_number = %s";
        $row = $wpdb->get_row($wpdb->prepare($query, $deal_number), ARRAY_A);
        return $row ?: null;
    }
}
