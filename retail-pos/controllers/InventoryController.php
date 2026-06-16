<?php
namespace RetailPosApi\Controllers;

use RetailPosApi\Repositories\InventoryRepository;
use RetailPosApi\Services\AuthService;
use WP_REST_Request;

class InventoryController extends BaseController {
    private $inventoryRepository;

    public function __construct() {
        $this->inventoryRepository = new InventoryRepository();
    }

    /**
     * GET /inventory
     */
    public function getAll(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_params();
        
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $where = ["i.deleted_at IS NULL"];
        $args = [];

        if (isset($params['product_id'])) {
            $where[] = "i.product_id = %d";
            $args[] = intval($params['product_id']);
        }

        $where_clause = implode(" AND ", $where);

        $total_query = "SELECT COUNT(*) FROM {$wpdb->prefix}pos_inventory i WHERE $where_clause";
        $total_count = !empty($args) ? (int)$wpdb->get_var($wpdb->prepare($total_query, $args)) : (int)$wpdb->get_var($total_query);

        $data_query = "SELECT i.*, p.product_name, p.sku, p.barcode, p.unit 
                       FROM {$wpdb->prefix}pos_inventory i
                       JOIN {$wpdb->prefix}pos_products p ON i.product_id = p.id
                       WHERE $where_clause
                       ORDER BY i.id DESC
                       LIMIT %d OFFSET %d";

        $data_args = array_merge($args, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_query, $data_args), ARRAY_A);

        return $this->success('Inventory tracking records retrieved.', [
            'total' => $total_count,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total_count / $limit),
            'data' => $rows ?: []
        ]);
    }

    /**
     * POST /inventory/adjust
     */
    public function adjustStock(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();
        $product_id = intval($params['product_id'] ?? 0);

        if (!$product_id || !isset($params['available_stock'])) {
            return $this->error('Validation failed: product_id and available_stock are required.');
        }

        $qty = floatval($params['available_stock']);
        $damaged = floatval($params['damaged_stock'] ?? 0.00);
        $remarks = sanitize_text_field($params['remarks'] ?? 'Manual stock adjustment');

        // Check if product exists
        $product_exists = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}pos_products WHERE id = %d AND deleted_at IS NULL", $product_id)) > 0;
        if (!$product_exists) {
            return $this->error('Product does not exist.');
        }

        // Update in products table
        $wpdb->update($wpdb->prefix . 'pos_products', ['stock_quantity' => $qty], ['id' => $product_id], ['%f'], ['%d']);

        // Update in inventory table
        $wpdb->update(
            $wpdb->prefix . 'pos_inventory',
            [
                'available_stock' => $qty,
                'damaged_stock' => $damaged
            ],
            ['product_id' => $product_id],
            ['%f', '%f'],
            ['%d']
        );

        AuthService::logActivity(get_current_user_id(), 'INVENTORY_ADJUST', "Adjusted stock for product ID: $product_id. New Qty: $qty Damaged: $damaged | Details: $remarks");

        return $this->success('Inventory levels adjusted successfully.', [
            'product_id' => $product_id,
            'available_stock' => $qty,
            'damaged_stock' => $damaged,
            'remarks' => $remarks
        ]);
    }

    /**
     * GET /inventory/low-stock
     */
    public function getLowStock(WP_REST_Request $request) {
        global $wpdb;
        $rows = $wpdb->get_results(
            "SELECT i.*, p.product_name, p.sku, p.barcode, p.unit 
             FROM {$wpdb->prefix}pos_inventory i
             JOIN {$wpdb->prefix}pos_products p ON i.product_id = p.id
             WHERE i.available_stock <= i.minimum_stock AND i.deleted_at IS NULL AND p.deleted_at IS NULL",
            ARRAY_A
        );
        return $this->success('Low stock items warnings retrieved.', $rows ?: []);
    }

    /**
     * GET /inventory/out-of-stock
     */
    public function getOutOfStock(WP_REST_Request $request) {
        global $wpdb;
        $rows = $wpdb->get_results(
            "SELECT i.*, p.product_name, p.sku, p.barcode, p.unit 
             FROM {$wpdb->prefix}pos_inventory i
             JOIN {$wpdb->prefix}pos_products p ON i.product_id = p.id
             WHERE i.available_stock <= 0 AND i.deleted_at IS NULL AND p.deleted_at IS NULL",
            ARRAY_A
        );
        return $this->success('Out of stock items checklist retrieved.', $rows ?: []);
    }
}
