<?php
namespace InventoryManagementApi\Repositories;

class PurchaseOrderRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('purchase_orders', true);
    }

    public function existsPoNumber(string $num, ?int $exclude_id = null): bool {
        return $this->exists('po_number', $num, $exclude_id);
    }

    /**
     * Get items associated with a Purchase Order
     */
    public function getPoItems(int $po_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_po_items';
        $products_table = $wpdb->prefix . 'inv_products';
        
        $query = "
            SELECT pi.*, p.product_name, p.sku, p.unit 
            FROM $table pi
            JOIN $products_table p ON pi.product_id = p.id
            WHERE pi.po_id = %d
        ";
        return $wpdb->get_results($wpdb->prepare($query, $po_id), ARRAY_A) ?: [];
    }

    /**
     * Add items to a Purchase Order
     */
    public function addPoItems(int $po_id, array $items): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_po_items';
        
        foreach ($items as $item) {
            $result = $wpdb->insert(
                $table,
                [
                    'po_id' => $po_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ],
                ['%d', '%d', '%d', '%f']
            );
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
