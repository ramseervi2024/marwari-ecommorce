<?php
namespace AccountingManagementApi\Repositories;

class PurchaseRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('purchases', true);
    }

    public function existsPurchaseNumber(string $purchase_number, ?int $exclude_id = null): bool {
        return $this->exists('purchase_number', $purchase_number, $exclude_id);
    }

    /**
     * Get items associated with a purchase bill
     */
    public function getPurchaseItems(int $purchase_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'acc_purchase_items';
        $items_table = $wpdb->prefix . 'acc_items';
        
        $query = "
            SELECT pi.*, i.item_name, i.item_code, i.hsn_sac_code 
            FROM $table pi
            JOIN $items_table i ON pi.item_id = i.id
            WHERE pi.purchase_id = %d
        ";
        return $wpdb->get_results($wpdb->prepare($query, $purchase_id), ARRAY_A) ?: [];
    }

    /**
     * Add items to a purchase bill
     */
    public function addPurchaseItems(int $purchase_id, array $items): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'acc_purchase_items';
        
        foreach ($items as $item) {
            $result = $wpdb->insert(
                $table,
                [
                    'purchase_id' => $purchase_id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'gst_percentage' => $item['gst_percentage'],
                    'gst_amount' => $item['gst_amount'],
                    'total_amount' => $item['total_amount']
                ],
                ['%d', '%d', '%d', '%f', '%f', '%f', '%f']
            );
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
