<?php
namespace InventoryManagementApi\Repositories;

class WarehouseRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('warehouses', true);
    }

    public function existsWarehouseCode(string $code, ?int $exclude_id = null): bool {
        return $this->exists('warehouse_code', $code, $exclude_id);
    }
}
