<?php
namespace RetailPosApi\Repositories;

class SaleRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('sales');
    }

    public function existsInvoiceNumber(string $invoice, ?int $exclude_id = null): bool {
        return $this->exists('invoice_number', $invoice, $exclude_id);
    }
}
