<?php
namespace AccountingManagementApi\Repositories;

class EinvoiceRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('einvoice', false);
    }

    public function findByInvoiceId(int $invoice_id): ?array {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE invoice_id = %d", $invoice_id),
            ARRAY_A
        );
        return $row ?: null;
    }
}
