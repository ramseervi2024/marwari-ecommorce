<?php
namespace CrmManagementApi\Repositories;

class PaymentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('payments', false);
    }

    public function findByInvoiceId(int $invoice_id): array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE invoice_id = %d ORDER BY payment_date DESC";
        $rows = $wpdb->get_results($wpdb->prepare($query, $invoice_id), ARRAY_A);
        return $rows ?: [];
    }
}
