<?php
namespace RetailPosApi\Controllers;

use RetailPosApi\Repositories\PurchaseRepository;
use RetailPosApi\Services\AuthService;
use WP_REST_Request;

class PurchaseController extends BaseController {
    private $purchaseRepository;

    public function __construct() {
        $this->purchaseRepository = new PurchaseRepository();
    }

    /**
     * GET /purchases
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'po_number', 'total_amount', 'purchase_date', 'status'];
        $search_fields = ['po_number', 'status'];

        $extra_filters = [];
        if (isset($params['supplier_id'])) {
            $extra_filters['supplier_id'] = intval($params['supplier_id']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->purchaseRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

        global $wpdb;
        $items = $results['data'];
        foreach ($items as &$item) {
            $item['supplier_name'] = $wpdb->get_var($wpdb->prepare("SELECT supplier_name FROM {$wpdb->prefix}pos_suppliers WHERE id = %d", $item['supplier_id'])) ?: '';
            $item['product_name'] = $wpdb->get_var($wpdb->prepare("SELECT product_name FROM {$wpdb->prefix}pos_products WHERE id = %d", $item['product_id'])) ?: '';
            $item['sku'] = $wpdb->get_var($wpdb->prepare("SELECT sku FROM {$wpdb->prefix}pos_products WHERE id = %d", $item['product_id'])) ?: '';
        }
        $results['data'] = $items;

        return $this->success('Purchase orders retrieved.', $results);
    }

    /**
     * GET /purchases/:id
     */
    public function getById(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));
        $purchase = $this->purchaseRepository->findById($id);

        if (!$purchase) {
            return $this->error('Purchase order not found.', [], 404);
        }

        $purchase['supplier_name'] = $wpdb->get_var($wpdb->prepare("SELECT supplier_name FROM {$wpdb->prefix}pos_suppliers WHERE id = %d", $purchase['supplier_id'])) ?: '';
        $purchase['product_name'] = $wpdb->get_var($wpdb->prepare("SELECT product_name FROM {$wpdb->prefix}pos_products WHERE id = %d", $purchase['product_id'])) ?: '';
        $purchase['sku'] = $wpdb->get_var($wpdb->prepare("SELECT sku FROM {$wpdb->prefix}pos_products WHERE id = %d", $purchase['product_id'])) ?: '';

        return $this->success('Purchase order details retrieved.', $purchase);
    }

    /**
     * POST /purchases (Book restock order from supplier)
     */
    public function create(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['supplier_id']) || empty($params['product_id']) || empty($params['quantity']) || empty($params['purchase_price'])) {
            return $this->error('Validation failed: supplier_id, product_id, quantity, and purchase_price are required.');
        }

        $supplier_id = intval($params['supplier_id']);
        $product_id = intval($params['product_id']);
        $qty = floatval($params['quantity']);
        $purchase_price = floatval($params['purchase_price']);

        // Check if supplier exists
        $supplier_exists = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}pos_suppliers WHERE id = %d AND deleted_at IS NULL", $supplier_id)) > 0;
        if (!$supplier_exists) {
            return $this->error('Supplier does not exist.');
        }

        // Get product details
        $product = $wpdb->get_row($wpdb->prepare("SELECT product_name, gst_percentage FROM {$wpdb->prefix}pos_products WHERE id = %d AND deleted_at IS NULL", $product_id), ARRAY_A);
        if (!$product) {
            return $this->error('Product does not exist.');
        }

        $gst_pct = floatval($product['gst_percentage']);
        $gst_amount = ($purchase_price * $qty) * ($gst_pct / 100);
        $total_amount = ($purchase_price * $qty) + $gst_amount;

        $po_number = 'PO-' . date('Ymd') . sprintf('%04d', rand(1000, 9999));

        $data = [
            'po_number' => $po_number,
            'supplier_id' => $supplier_id,
            'product_id' => $product_id,
            'quantity' => $qty,
            'purchase_price' => $purchase_price,
            'gst_amount' => $gst_amount,
            'total_amount' => $total_amount,
            'purchase_date' => current_time('mysql'),
            'status' => 'RECEIVED'
        ];

        $inserted_id = $this->purchaseRepository->create($data, ['%s', '%d', '%d', '%f', '%f', '%f', '%f', '%s', '%s']);
        if (!$inserted_id) {
            return $this->error('Failed to book purchase order.');
        }

        // Increment stock level in products and inventory
        $wpdb->query(
            $wpdb->prepare("UPDATE {$wpdb->prefix}pos_products SET stock_quantity = stock_quantity + %f, purchase_price = %f WHERE id = %d", $qty, $purchase_price, $product_id)
        );
        $wpdb->query(
            $wpdb->prepare("UPDATE {$wpdb->prefix}pos_inventory SET available_stock = available_stock + %f WHERE product_id = %d", $qty, $product_id)
        );

        AuthService::logActivity(get_current_user_id(), 'PURCHASE_CREATE', "Restocked product ID: $product_id quantity: $qty via PO: $po_number");

        return $this->success('Purchase order created successfully, inventory quantities incremented.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /purchases/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $purchase = $this->purchaseRepository->findById($id);

        if (!$purchase) {
            return $this->error('Purchase order not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['status'])) {
            $data['status'] = sanitize_text_field($params['status']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->purchaseRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update purchase order.');
        }

        AuthService::logActivity(get_current_user_id(), 'PURCHASE_UPDATE', "Updated status of PO ID: $id");

        return $this->success('Purchase order updated successfully.', $this->purchaseRepository->findById($id));
    }

    /**
     * DELETE /purchases/:id (Void purchase order and reverse inventory increment)
     */
    public function delete(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));
        $purchase = $this->purchaseRepository->findById($id);

        if (!$purchase) {
            return $this->error('Purchase order not found.', [], 404);
        }

        if ($purchase['status'] === 'CANCELLED') {
            return $this->error('This purchase order is already cancelled.');
        }

        // Check if there is enough stock to reverse the purchase
        $product_id = intval($purchase['product_id']);
        $qty = floatval($purchase['quantity']);
        $current_stock = floatval($wpdb->get_var($wpdb->prepare("SELECT stock_quantity FROM {$wpdb->prefix}pos_products WHERE id = %d", $product_id)));

        if ($current_stock < $qty) {
            return $this->error("Cannot cancel purchase order: Restocking quantity exceeds currently available inventory stock.");
        }

        // Deduct quantities back
        $wpdb->query(
            $wpdb->prepare("UPDATE {$wpdb->prefix}pos_products SET stock_quantity = stock_quantity - %f WHERE id = %d", $qty, $product_id)
        );
        $wpdb->query(
            $wpdb->prepare("UPDATE {$wpdb->prefix}pos_inventory SET available_stock = available_stock - %f WHERE product_id = %d", $qty, $product_id)
        );

        // Cancel order
        $wpdb->update($wpdb->prefix . 'pos_purchases', ['status' => 'CANCELLED', 'deleted_at' => current_time('mysql')], ['id' => $id], ['%s', '%s'], ['%d']);

        AuthService::logActivity(get_current_user_id(), 'PURCHASE_CANCEL', "Cancelled PO: {$purchase['po_number']} ID: $id, reversed stock levels");

        return $this->success('Purchase order cancelled and stock reversed successfully.');
    }
}
