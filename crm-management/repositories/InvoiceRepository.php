<?php
namespace CrmManagementApi\Repositories;

class InvoiceRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('invoices', false);
    }

    public function findByInvoiceNumber(string $invoice_number): ?array {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} WHERE invoice_number = %s";
        $row = $wpdb->get_row($wpdb->prepare($query, $invoice_number), ARRAY_A);
        return $row ?: null;
    }
}
