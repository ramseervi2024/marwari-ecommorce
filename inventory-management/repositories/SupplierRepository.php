<?php
namespace InventoryManagementApi\Repositories;

class SupplierRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('suppliers', true);
    }

    public function existsSupplierCode(string $code, ?int $exclude_id = null): bool {
        return $this->exists('supplier_code', $code, $exclude_id);
    }
}
