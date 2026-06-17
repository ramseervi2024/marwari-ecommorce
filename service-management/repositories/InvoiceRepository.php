<?php
namespace ServiceManagementApi\Repositories;

class InvoiceRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('invoices', true);
    }

    public function existsInvoiceNumber(string $num, ?int $exclude_id = null): bool {
        return $this->exists('invoice_number', $num, $exclude_id);
    }
}
