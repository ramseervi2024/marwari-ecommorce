<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\InvoiceRepository;
use RestaurantManagementApi\Repositories\OrderRepository;
use RestaurantManagementApi\Repositories\CustomerRepository;
use WP_REST_Request;

class InvoiceController extends BaseController {
    private $repository;
    private $orderRepo;
    private $customerRepo;

    public function __construct() {
        $this->repository = new InvoiceRepository();
        $this->orderRepo = new OrderRepository();
        $this->customerRepo = new CustomerRepository();
    }

    public function getInvoices(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $invoices = $this->repository->all($limit, $offset);
        return $this->success('Invoices retrieved successfully.', $invoices);
    }

    public function createInvoice(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['order_id']) || empty($params['payment_method'])) {
            return $this->error('Validation failed: order_id and payment_method are required.');
        }

        $order_id = intval($params['order_id']);
        $order = $this->orderRepo->find($order_id);
        if (!$order) {
            return $this->error('Order not found.');
        }

        // Check if invoice already exists
        $existing = $this->repository->findByOrderId($order_id);
        if ($existing) {
            return $this->success('Invoice already exists for this order.', $existing);
        }

        $invoice_number = 'INV-' . strtoupper(bin2hex(random_bytes(3)));
        $subtotal = floatval($order['subtotal']);
        $discount = floatval($params['discount'] ?? $order['discount']);
        $tax = floatval($order['tax']);
        $service_charge = floatval($params['service_charge'] ?? 0.00);

        $total_amount = ($subtotal + $tax + $service_charge) - $discount;

        $customer_id = !empty($params['customer_id']) ? intval($params['customer_id']) : null;

        $data = [
            'invoice_number' => $invoice_number,
            'order_id' => $order_id,
            'customer_id' => $customer_id,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'service_charge' => $service_charge,
            'total_amount' => $total_amount,
            'payment_method' => sanitize_text_field($params['payment_method']),
            'status' => 'Paid'
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to create invoice record.');
        }

        // Complete order status and release table
        $this->orderRepo->update($order_id, ['status' => 'Completed']);
        if (!empty($order['table_id'])) {
            global $wpdb;
            $t_tables = $wpdb->prefix . 'restaurant_tables';
            $wpdb->update($t_tables, ['status' => 'Cleaning'], ['id' => intval($order['table_id'])]);
        }

        // Add Loyalty Points to Customer if customer exists
        if ($customer_id) {
            $customer = $this->customerRepo->find($customer_id);
            if ($customer) {
                // Earn 1 point per 10 Rupees/Dollars spent
                $earned_points = floor($total_amount / 10);
                
                global $wpdb;
                $t_customers = $wpdb->prefix . 'restaurant_customers';
                $wpdb->query($wpdb->prepare(
                    "UPDATE {$t_customers} 
                     SET loyalty_points = loyalty_points + %d, total_orders = total_orders + 1 
                     WHERE id = %d",
                    $earned_points,
                    $customer_id
                ));
            }
        }

        $data['id'] = $id;
        return $this->success('Invoice created successfully and order marked completed.', $data, 201);
    }
}
