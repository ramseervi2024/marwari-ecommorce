<?php
namespace CrmManagementApi\Repositories;

class LeadRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('leads', true);
    }

    public function findByLeadNumber(string $lead_number): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE lead_number = %s AND deleted_at IS NULL";
        $row = $wpdb->get_row($wpdb->prepare($query, $lead_number), ARRAY_A);
        return $row ?: null;
    }
}
