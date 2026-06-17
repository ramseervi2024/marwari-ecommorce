<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\BillingRepository;
use JewelleryManagementApi\Repositories\InventoryRepository;
use JewelleryManagementApi\Repositories\CustomerRepository;
use JewelleryManagementApi\Repositories\LoyaltyRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class BillingController extends BaseController {
    private $repo;
    private $inventoryRepo;
    private $customerRepo;
    private $loyaltyRepo;

    public function __construct() {
        $this->repo = new BillingRepository();
        $this->inventoryRepo = new InventoryRepository();
        $this->customerRepo = new CustomerRepository();
        $this->loyaltyRepo = new LoyaltyRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Billing invoices retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Invoice not found.', [], 404);
        }
        return $this->success('Invoice retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['customer_id']) || empty($params['product_id'])) {
            return $this->error('Validation failed: customer_id and product_id are required.');
        }

        $customer_id = intval($params['customer_id']);
        $product_id = intval($params['product_id']);

        // Check if customer exists
        $customer = $this->customerRepo->find($customer_id);
        if (!$customer) {
            return $this->error('Customer not found.');
        }

        // Check if product exists and is ACTIVE
        $product = $this->inventoryRepo->find($product_id);
        if (!$product) {
            return $this->error('Finished ornament not found.');
        }
        if ($product['status'] === 'SOLD') {
            return $this->error('This ornament has already been sold.');
        }

        $gross_weight = floatval($params['gross_weight'] ?? $product['gross_weight']);
        $net_weight = floatval($params['net_weight'] ?? $product['net_weight']);
        $gold_rate = floatval($params['gold_rate'] ?? 0);
        $silver_rate = floatval($params['silver_rate'] ?? 0);
        
        // Making charges logic: can be per-gram of net weight, or flat
        $making_rate_or_total = floatval($params['making_charges'] ?? $product['making_charges']);
        $making_charges = $making_rate_or_total;
        // If it's a standard per-gram charge (e.g. less than 10000 per gram), compute it
        if ($making_rate_or_total > 0 && $making_rate_or_total < 5000) {
            $making_charges = $net_weight * $making_rate_or_total;
        }

        $stone_charges = floatval($params['stone_charges'] ?? 0);
        $discount = floatval($params['discount'] ?? 0);

        // Subtotal calculation
        $metal_rate = ($product['metal_type'] === 'Silver') ? $silver_rate : $gold_rate;
        $metal_value = $net_weight * $metal_rate;
        if ($metal_value <= 0) {
            // fallback if rates are not set
            $metal_value = floatval($product['selling_price']) ?: 1000;
        }

        $subtotal = $metal_value + $making_charges + $stone_charges - $discount;
        if ($subtotal < 0) {
            $subtotal = 0;
        }

        // 3% standard GST on jewelry
        $gst_amount = round($subtotal * 0.03, 2);
        $total_amount = $subtotal + $gst_amount;

        $invoice_data = [
            'invoice_number' => sanitize_text_field($params['invoice_number'] ?? 'INV-' . date('Ymd') . '-' . rand(1000, 9999)),
            'customer_id' => $customer_id,
            'product_id' => $product_id,
            'gross_weight' => $gross_weight,
            'net_weight' => $net_weight,
            'gold_rate' => $gold_rate,
            'silver_rate' => $silver_rate,
            'making_charges' => $making_charges,
            'stone_charges' => $stone_charges,
            'gst_amount' => $gst_amount,
            'discount' => $discount,
            'total_amount' => $total_amount,
            'payment_method' => sanitize_text_field($params['payment_method'] ?? 'CASH'),
            'invoice_date' => current_time('mysql'),
            'status' => sanitize_text_field($params['status'] ?? 'PAID'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ];

        $invoice_id = $this->repo->create($invoice_data);
        if (!$invoice_id) {
            return $this->error('Failed to create billing invoice.');
        }

        // Deactivate finished item by marking as SOLD
        $this->inventoryRepo->update($product_id, ['status' => 'SOLD', 'updated_at' => current_time('mysql')]);

        // Award Customer Loyalty Points: 1 point per 1000 INR of total amount
        $points_earned = intval(floor($total_amount / 1000));
        if ($points_earned > 0) {
            $new_points = intval($customer['loyalty_points']) + $points_earned;
            $this->customerRepo->update($customer_id, ['loyalty_points' => $new_points]);
            
            // Add entry to loyalty log
            $this->loyaltyRepo->create([
                'customer_id' => $customer_id,
                'points_earned' => $points_earned,
                'points_redeemed' => 0,
                'membership_level' => ($new_points > 100) ? 'Platinum' : (($new_points > 50) ? 'Gold' : 'Silver'),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]);
        }

        AuthService::logActivity(get_current_user_id(), 'BILLING_CREATE', "Generated GST Invoice {$invoice_data['invoice_number']} for Customer ID $customer_id, Total: {$total_amount} INR");

        return $this->success('Billing invoice created successfully.', array_merge(['id' => $invoice_id], $invoice_data), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Invoice not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['payment_method'])) $updates['payment_method'] = sanitize_text_field($params['payment_method']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILLING_UPDATE', "Updated invoice ID $id status to {$params['status']}");

        return $this->success('Invoice updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Invoice not found.', [], 404);
        }
        
        // Revert product status if deleting invoice
        $this->inventoryRepo->update($item['product_id'], ['status' => 'ACTIVE', 'updated_at' => current_time('mysql')]);

        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILLING_DELETE', "Deleted invoice ID $id");

        return $this->success('Invoice deleted successfully.');
    }
}
