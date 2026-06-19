<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class OrderRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('orders', true);
    }

    public function getItems(int $order_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'wholesale_order_items';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE order_id = %d", $order_id), ARRAY_A) ?: [];
    }

    public function saveItems(int $order_id, array $items) {
        global $wpdb;
        $table = $wpdb->prefix . 'wholesale_order_items';
        // Remove existing items first
        $wpdb->delete($table, ['order_id' => $order_id], ['%d']);
        foreach ($items as $item) {
            $wpdb->insert($table, [
                'order_id'       => $order_id,
                'product_id'     => $item['product_id'],
                'quantity'       => $item['quantity'],
                'unit_price'     => $item['unit_price'],
                'discount'       => $item['discount'] ?? 0.00,
                'gst_percentage' => $item['gst_percentage'] ?? 0.00,
                'gst_amount'     => $item['gst_amount'] ?? 0.00,
                'total'          => $item['total'],
            ], ['%d', '%d', '%d', '%f', '%f', '%f', '%f', '%f']);
        }
    }
}
