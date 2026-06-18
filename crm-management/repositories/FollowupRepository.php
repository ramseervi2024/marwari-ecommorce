<?php
namespace CrmManagementApi\Repositories;

class FollowupRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('followups', false);
    }

    public function findByLeadId(int $lead_id): array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE lead_id = %d ORDER BY followup_date DESC, followup_time DESC";
        $rows = $wpdb->get_results($wpdb->prepare($query, $lead_id), ARRAY_A);
        return $rows ?: [];
    }
}
