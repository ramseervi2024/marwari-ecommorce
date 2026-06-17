<?php
namespace InventoryManagementApi\Repositories;

class OutwardRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('stock_outward', false);
    }

    /**
     * Get items associated with an Outward slip
     */
    public function getOutwardItems(int $outward_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_outward_items';
        $products_table = $wpdb->prefix . 'inv_products';
        $warehouses_table = $wpdb->prefix . 'inv_warehouses';
        
        $query = "
            SELECT oi.*, p.product_name, p.sku, p.unit, w.warehouse_name 
            FROM $table oi
            JOIN $products_table p ON oi.product_id = p.id
            JOIN $warehouses_table w ON oi.warehouse_id = w.id
            WHERE oi.outward_id = %d
        ";
        return $wpdb->get_results($wpdb->prepare($query, $outward_id), ARRAY_A) ?: [];
    }

    /**
     * Add items to an Outward slip
     */
    public function addOutwardItems(int $outward_id, array $items): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_outward_items';
        
        foreach ($items as $item) {
            $result = $wpdb->insert(
                $table,
                [
                    'outward_id' => $outward_id,
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $item['warehouse_id'],
                    'quantity' => $item['quantity']
                ],
                ['%d', '%d', '%d', '%d']
            );
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
