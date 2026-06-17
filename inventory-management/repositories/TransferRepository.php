<?php
namespace InventoryManagementApi\Repositories;

class TransferRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('transfers', true);
    }

    public function existsTransferNumber(string $num, ?int $exclude_id = null): bool {
        return $this->exists('transfer_number', $num, $exclude_id);
    }

    /**
     * Get items associated with a Transfer
     */
    public function getTransferItems(int $transfer_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_transfer_items';
        $products_table = $wpdb->prefix . 'inv_products';
        
        $query = "
            SELECT ti.*, p.product_name, p.sku, p.unit 
            FROM $table ti
            JOIN $products_table p ON ti.product_id = p.id
            WHERE ti.transfer_id = %d
        ";
        return $wpdb->get_results($wpdb->prepare($query, $transfer_id), ARRAY_A) ?: [];
    }

    /**
     * Add items to a Transfer
     */
    public function addTransferItems(int $transfer_id, array $items): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_transfer_items';
        
        foreach ($items as $item) {
            $result = $wpdb->insert(
                $table,
                [
                    'transfer_id' => $transfer_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ],
                ['%d', '%d', '%d']
            );
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
