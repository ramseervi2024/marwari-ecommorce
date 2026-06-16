<?php
namespace RetailPosApi\Controllers;

use RetailPosApi\Repositories\SaleRepository;
use RetailPosApi\Services\AuthService;
use WP_REST_Request;

class SaleController extends BaseController {
    private $saleRepository;

    public function __construct() {
        $this->saleRepository = new SaleRepository();
    }

    /**
     * GET /sales
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'invoice_number', 'total_amount', 'invoice_date', 'status'];
        $search_fields = ['invoice_number', 'payment_method'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['customer_id'])) {
            $extra_filters['customer_id'] = intval($params['customer_id']);
        }

        $results = $this->saleRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        global $wpdb;
        $items = $results['data'];
        foreach ($items as &$item) {
            if ($item['customer_id']) {
                $item['customer_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pos_customers WHERE id = %d", $item['customer_id'])) ?: 'Walk-in Customer';
                $item['customer_code'] = $wpdb->get_var($wpdb->prepare("SELECT customer_code FROM {$wpdb->prefix}pos_customers WHERE id = %d", $item['customer_id'])) ?: '';
            } else {
                $item['customer_name'] = 'Walk-in Customer';
                $item['customer_code'] = '';
            }
        }
        $results['data'] = $items;

        return $this->success('Sales records retrieved.', $results);
    }

    /**
     * GET /sales/:id
     */
    public function getById(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));
        $sale = $this->saleRepository->findById($id);

        if (!$sale) {
            return $this->error('Invoice not found.', [], 404);
        }

        // Fetch invoice items
        $items = $wpdb->get_results(
            $wpdb->prepare("SELECT si.*, p.product_name, p.sku, p.barcode 
                            FROM {$wpdb->prefix}pos_sale_items si
                            JOIN {$wpdb->prefix}pos_products p ON si.product_id = p.id
                            WHERE si.sale_id = %d", $id),
            ARRAY_A
        );

        if ($sale['customer_id']) {
            $customer = $wpdb->get_row($wpdb->prepare("SELECT name, customer_code, mobile, email, gst_number FROM {$wpdb->prefix}pos_customers WHERE id = %d", $sale['customer_id']), ARRAY_A);
            $sale['customer'] = $customer ?: null;
        } else {
            $sale['customer'] = null;
        }

        $sale['items'] = $items ?: [];

        return $this->success('Invoice details retrieved.', $sale);
    }

    /**
     * POST /sales (POS Billing checkout)
     */
    public function create(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['items']) || !is_array($params['items'])) {
            return $this->error('Validation failed: items array is required.');
        }

        $customer_id = !empty($params['customer_id']) ? intval($params['customer_id']) : null;
        $discount = floatval($params['discount'] ?? 0.00);
        $payment_method = sanitize_text_field($params['payment_method'] ?? 'Cash');

        // Generate invoice number
        $invoice_number = 'INV-' . date('Ymd') . sprintf('%04d', rand(1000, 9999));
        while ($this->saleRepository->existsInvoiceNumber($invoice_number)) {
            $invoice_number = 'INV-' . date('Ymd') . sprintf('%04d', rand(1000, 9999));
        }

        $subtotal_sum = 0.00;
        $gst_sum = 0.00;
        $total_sum = 0.00;
        $processed_items = [];

        // Begin Transaction equivalent block
        foreach ($params['items'] as $item) {
            $product_id = intval($item['product_id']);
            $qty = floatval($item['quantity']);

            if ($qty <= 0) continue;

            // Retrieve product details
            $product = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}pos_products WHERE id = %d AND deleted_at IS NULL", $product_id),
                ARRAY_A
            );

            if (!$product) {
                return $this->error("Product ID $product_id does not exist.");
            }

            // Check stock level
            $avail = floatval($product['stock_quantity']);
            if ($avail < $qty) {
                return $this->error("Insufficient stock: Product '{$product['product_name']}' only has {$avail} {$product['unit']} available.");
            }

            // Calculations (Inclusive of GST)
            $selling_price = floatval($product['selling_price']);
            $purchase_price = floatval($product['purchase_price']);
            $gst_pct = floatval($product['gst_percentage']);

            $price_without_gst = $selling_price / (1 + ($gst_pct / 100));
            $gst_per_unit = $selling_price - $price_without_gst;
            
            $item_subtotal = $price_without_gst * $qty;
            $item_gst = $gst_per_unit * $qty;
            $item_total = $selling_price * $qty;

            $subtotal_sum += $item_subtotal;
            $gst_sum += $item_gst;
            $total_sum += $item_total;

            $processed_items[] = [
                'product_id' => $product_id,
                'quantity' => $qty,
                'purchase_price' => $purchase_price,
                'selling_price' => $selling_price,
                'gst_percentage' => $gst_pct,
                'gst_amount' => $item_gst,
                'discount' => 0.00, // Line item discounts can be added later
                'total' => $item_total
            ];
        }

        if (empty($processed_items)) {
            return $this->error('Failed to create sale: No valid items found.');
        }

        $grand_total = max(0.00, $total_sum - $discount);

        // 1. Create Sale Header
        $sale_data = [
            'invoice_number' => $invoice_number,
            'customer_id' => $customer_id,
            'subtotal' => $subtotal_sum,
            'discount' => $discount,
            'gst_amount' => $gst_sum,
            'total_amount' => $grand_total,
            'payment_method' => $payment_method,
            'invoice_date' => current_time('mysql'),
            'status' => 'COMPLETED'
        ];

        $header_result = $wpdb->insert($wpdb->prefix . 'pos_sales', $sale_data, ['%s', '%d', '%f', '%f', '%f', '%f', '%s', '%s', '%s']);
        if ($header_result === false) {
            return $this->error('Database transaction failed on sale checkout.');
        }
        $sale_id = intval($wpdb->insert_id);

        // 2. Insert items and decrement stock
        foreach ($processed_items as $p_item) {
            $p_item['sale_id'] = $sale_id;
            $wpdb->insert($wpdb->prefix . 'pos_sale_items', $p_item, ['%d', '%d', '%f', '%f', '%f', '%f', '%f', '%f', '%f']);

            // Deduct stock from products catalog
            $wpdb->query(
                $wpdb->prepare("UPDATE {$wpdb->prefix}pos_products SET stock_quantity = stock_quantity - %f WHERE id = %d", $p_item['quantity'], $p_item['product_id'])
            );

            // Deduct stock from inventory table
            $wpdb->query(
                $wpdb->prepare("UPDATE {$wpdb->prefix}pos_inventory SET available_stock = available_stock - %f WHERE product_id = %d", $p_item['quantity'], $p_item['product_id'])
            );
        }

        // 3. Accumulate Customer loyalty points (₹100 = 1 loyalty point)
        if ($customer_id && $grand_total > 0) {
            $points_earned = floor($grand_total / 100);
            if ($points_earned > 0) {
                // Update customer total points and sales value
                $wpdb->query(
                    $wpdb->prepare("UPDATE {$wpdb->prefix}pos_customers 
                                    SET loyalty_points = loyalty_points + %d, total_purchases = total_purchases + %f 
                                    WHERE id = %d", $points_earned, $grand_total, $customer_id)
                );

                // Log points transaction
                $wpdb->insert($wpdb->prefix . 'pos_loyalty', [
                    'customer_id' => $customer_id,
                    'points' => $points_earned,
                    'transaction_type' => 'EARNED',
                    'sale_id' => $sale_id,
                    'remarks' => "Earned on invoice $invoice_number"
                ], ['%d', '%d', '%s', '%d', '%s']);
            }
        }

        AuthService::logActivity(get_current_user_id(), 'SALE_CREATE', "POS Sale completed: $invoice_number Total: ₹$grand_total");

        return $this->success('Sale transaction booked successfully.', array_merge(['id' => $sale_id], $sale_data), 201);
    }

    /**
     * DELETE /sales/:id (Void invoice and restore inventory)
     */
    public function delete(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));
        $sale = $this->saleRepository->findById($id);

        if (!$sale) {
            return $this->error('Invoice not found.', [], 404);
        }

        if ($sale['status'] === 'RETURNED') {
            return $this->error('This invoice has already been voided/returned.');
        }

        // 1. Restore stock quantity for each item
        $items = $wpdb->get_results($wpdb->prepare("SELECT product_id, quantity FROM {$wpdb->prefix}pos_sale_items WHERE sale_id = %d", $id), ARRAY_A);
        foreach ($items as $item) {
            $wpdb->query(
                $wpdb->prepare("UPDATE {$wpdb->prefix}pos_products SET stock_quantity = stock_quantity + %f WHERE id = %d", $item['quantity'], $item['product_id'])
            );
            $wpdb->query(
                $wpdb->prepare("UPDATE {$wpdb->prefix}pos_inventory SET available_stock = available_stock + %f WHERE product_id = %d", $item['quantity'], $item['product_id'])
            );
        }

        // 2. Revoke loyalty points if customer is linked
        if ($sale['customer_id']) {
            $points_revoked = floor($sale['total_amount'] / 100);
            if ($points_revoked > 0) {
                $wpdb->query(
                    $wpdb->prepare("UPDATE {$wpdb->prefix}pos_customers 
                                    SET loyalty_points = GREATEST(0, loyalty_points - %d), total_purchases = GREATEST(0.00, total_purchases - %f) 
                                    WHERE id = %d", $points_revoked, $sale['total_amount'], $sale['customer_id'])
                );

                $wpdb->insert($wpdb->prefix . 'pos_loyalty', [
                    'customer_id' => $sale['customer_id'],
                    'points' => -$points_revoked,
                    'transaction_type' => 'REDEEMED',
                    'remarks' => "Revoked due to invoice cancellation: {$sale['invoice_number']}"
                ], ['%d', '%d', '%s', '%s']);
            }
        }

        // 3. Mark invoice as RETURNED
        $wpdb->update($wpdb->prefix . 'pos_sales', ['status' => 'RETURNED', 'deleted_at' => current_time('mysql')], ['id' => $id], ['%s', '%s'], ['%d']);

        AuthService::logActivity(get_current_user_id(), 'SALE_VOID', "Voided POS Sale Invoice: {$sale['invoice_number']} ID: $id");

        return $this->success('POS Invoice voided successfully and inventory stock restored.');
    }
}
