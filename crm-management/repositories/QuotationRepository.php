<?php
namespace CrmManagementApi\Repositories;

class QuotationRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('quotations', false);
    }

    public function findByQuotationNumber(string $quotation_number): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE quotation_number = %s";
        $row = $wpdb->get_row($wpdb->prepare($query, $quotation_number), ARRAY_A);
        return $row ?: null;
    }
}
