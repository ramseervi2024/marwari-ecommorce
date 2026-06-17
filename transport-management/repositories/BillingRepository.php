<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class BillingRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('billing');
    }

    public function existsInvoiceNumber(string $invoice_number, ?int $exclude_id = null): bool {
        return $this->exists('invoice_number', $invoice_number, $exclude_id);
    }
}
