<?php
namespace AccountingManagementApi\Repositories;

class GstRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('gst', false);
    }

    /**
     * Get GST report by tax period (e.g. "2026-06")
     */
    public function getGstReport(string $tax_period): array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE tax_period = %s";
        return $wpdb->get_results($wpdb->prepare($query, $tax_period), ARRAY_A) ?: [];
    }
}
