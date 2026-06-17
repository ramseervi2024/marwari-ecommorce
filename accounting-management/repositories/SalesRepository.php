<?php
namespace AccountingManagementApi\Repositories;

class SalesRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('sales', true);
    }

    public function existsInvoiceNumber(string $invoice_number, ?int $exclude_id = null): bool {
        return $this->exists('invoice_number', $invoice_number, $exclude_id);
    }

    /**
     * Get items associated with a sales invoice
     */
    public function getInvoiceItems(int $sale_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'acc_sale_items';
        $items_table = $wpdb->prefix . 'acc_items';
        
        $query = "
            SELECT si.*, i.item_name, i.item_code, i.hsn_sac_code 
            FROM $table si
            JOIN $items_table i ON si.item_id = i.id
            WHERE si.sale_id = %d
        ";
        return $wpdb->get_results($wpdb->prepare($query, $sale_id), ARRAY_A) ?: [];
    }

    /**
     * Add items to a sales invoice
     */
    public function addInvoiceItems(int $sale_id, array $items): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'acc_sale_items';
        
        foreach ($items as $item) {
            $result = $wpdb->insert(
                $table,
                [
                    'sale_id' => $sale_id,
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
