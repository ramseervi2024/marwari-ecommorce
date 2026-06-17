<?php
namespace InventoryManagementApi\Repositories;

class ProductRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('products', true);
    }

    public function existsSku(string $sku, ?int $exclude_id = null): bool {
        return $this->exists('sku', $sku, $exclude_id);
    }

    public function existsBarcode(string $barcode, ?int $exclude_id = null): bool {
        if (empty($barcode)) return false;
        return $this->exists('barcode', $barcode, $exclude_id);
    }
}
