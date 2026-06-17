<?php
namespace InventoryManagementApi\Repositories;

class StockRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('stock', false);
    }

    /**
     * Get stock count for a specific product and warehouse
     */
    public function getStockRecord(int $product_id, int $warehouse_id): ?array {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE product_id = %d AND warehouse_id = %d", $product_id, $warehouse_id),
            ARRAY_A
        );
        return $row ?: null;
    }

    /**
     * Adjust stock levels for a product at a warehouse. Creates record if it doesn't exist.
     */
    public function adjustStock(int $product_id, int $warehouse_id, int $available_change, int $reserved_change = 0, int $damaged_change = 0): bool {
        global $wpdb;
        $existing = $this->getStockRecord($product_id, $warehouse_id);

        if ($existing) {
            $new_avail = max(0, (int)$existing['available_stock'] + $available_change);
            $new_res = max(0, (int)$existing['reserved_stock'] + $reserved_change);
            $new_dmg = max(0, (int)$existing['damaged_stock'] + $damaged_change);

            $result = $wpdb->update(
                $this->table_name,
                [
                    'available_stock' => $new_avail,
                    'reserved_stock' => $new_res,
                    'damaged_stock' => $new_dmg
                ],
                ['id' => $existing['id']],
                ['%d', '%d', '%d'],
                ['%d']
            );
            return $result !== false;
        } else {
            $new_avail = max(0, $available_change);
            $new_res = max(0, $reserved_change);
            $new_dmg = max(0, $damaged_change);

            $result = $wpdb->insert(
                $this->table_name,
                [
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id,
                    'available_stock' => $new_avail,
                    'reserved_stock' => $new_res,
                    'damaged_stock' => $new_dmg
                ],
                ['%d', '%d', '%d', '%d', '%d']
            );
            return $result !== false;
        }
    }
}
