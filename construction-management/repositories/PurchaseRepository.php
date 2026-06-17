<?php
namespace ConstructionManagementApi\Repositories;

class PurchaseRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('purchases');
    }

    public function existsPurchaseOrderNumber(string $po_number, ?int $exclude_id = null): bool {
        return $this->exists('purchase_order_number', $po_number, $exclude_id);
    }
}
