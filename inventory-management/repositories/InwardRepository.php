<?php
namespace InventoryManagementApi\Repositories;

class InwardRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('stock_inward', false);
    }

    /**
     * Get items associated with an Inward slip
     */
    public function getInwardItems(int $inward_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_inward_items';
        $products_table = $wpdb->prefix . 'inv_products';
        $warehouses_table = $wpdb->prefix . 'inv_warehouses';
        
        $query = "
            SELECT ii.*, p.product_name, p.sku, p.unit, w.warehouse_name 
            FROM $table ii
            JOIN $products_table p ON ii.product_id = p.id
            JOIN $warehouses_table w ON ii.warehouse_id = w.id
            WHERE ii.inward_id = %d
        ";
        return $wpdb->get_results($wpdb->prepare($query, $inward_id), ARRAY_A) ?: [];
    }

    /**
     * Add items to an Inward slip
     */
    public function addInwardItems(int $inward_id, array $items): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_inward_items';
        
        foreach ($items as $item) {
            $result = $wpdb->insert(
                $table,
                [
                    'inward_id' => $inward_id,
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $item['warehouse_id'],
                    'quantity' => $item['quantity'],
                    'batch_number' => $item['batch_number'] ?? ''
                ],
                ['%d', '%d', '%d', '%d', '%s']
            );
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
