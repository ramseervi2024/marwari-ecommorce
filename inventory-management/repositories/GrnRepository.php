<?php
namespace InventoryManagementApi\Repositories;

class GrnRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('grn', false);
    }

    public function existsGrnNumber(string $num, ?int $exclude_id = null): bool {
        return $this->exists('grn_number', $num, $exclude_id);
    }

    /**
     * Get items associated with a GRN
     */
    public function getGrnItems(int $grn_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_grn_items';
        $products_table = $wpdb->prefix . 'inv_products';
        
        $query = "
            SELECT gi.*, p.product_name, p.sku, p.unit 
            FROM $table gi
            JOIN $products_table p ON gi.product_id = p.id
            WHERE gi.grn_id = %d
        ";
        return $wpdb->get_results($wpdb->prepare($query, $grn_id), ARRAY_A) ?: [];
    }

    /**
     * Add items to a GRN
     */
    public function addGrnItems(int $grn_id, array $items): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_grn_items';
        
        foreach ($items as $item) {
            $result = $wpdb->insert(
                $table,
                [
                    'grn_id' => $grn_id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => $item['quantity_received']
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
