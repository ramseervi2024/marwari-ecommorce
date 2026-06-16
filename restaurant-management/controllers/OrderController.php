<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\OrderRepository;
use WP_REST_Request;

class OrderController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new OrderRepository();
    }

    public function getOrders(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $orders = $this->repository->all($limit, $offset);
        
        // Load items for each order
        foreach ($orders as &$order) {
            $order['order_items'] = $this->repository->getItems($order['id']);
        }
        return $this->success('Orders retrieved successfully.', $orders);
    }

    public function getOrder(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $order = $this->repository->find($id);
        if (!$order) {
            return $this->error('Order not found.', [], 404);
        }
        $order['order_items'] = $this->repository->getItems($id);
        return $this->success('Order details retrieved successfully.', $order);
    }

    public function createOrder(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['order_items'])) {
            return $this->error('Validation failed: order_items list is required.');
        }

        $order_number = 'ORD-' . strtoupper(bin2hex(random_bytes(3)));

        // Sum subtotal, discount, tax, total
        $subtotal = 0.00;
        $tax = 0.00;
        foreach ($params['order_items'] as $item) {
            $qty = intval($item['quantity'] ?? 1);
            $price = floatval($item['price']);
            $item_tax_pct = floatval($item['tax_percentage'] ?? 5.00);
            
            $item_subtotal = $price * $qty;
            $item_tax = $item_subtotal * ($item_tax_pct / 100);
            
            $subtotal += $item_subtotal;
            $tax += $item_tax;
        }

        $discount = floatval($params['discount'] ?? 0.00);
        $total_amount = ($subtotal + $tax) - $discount;

        $data = [
            'order_number' => $order_number,
            'table_id' => !empty($params['table_id']) ? intval($params['table_id']) : null,
            'waiter_id' => get_current_user_id() ?: null,
            'customer_name' => sanitize_text_field($params['customer_name'] ?? 'Guest'),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total_amount' => $total_amount,
            'status' => sanitize_text_field($params['status'] ?? 'Pending')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to create order record.');
        }

        $this->repository->saveItems($id, $params['order_items']);

        // Check if starting in Preparing status
        if ($data['status'] === 'Preparing') {
            $this->repository->deductInventory($id);
        }

        $data['id'] = $id;
        $data['order_items'] = $this->repository->getItems($id);

        return $this->success('Order created successfully.', $data, 201);
    }

    public function updateOrder(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $order = $this->repository->find($id);
        if (!$order) {
            return $this->error('Order not found.', [], 404);
        }

        $data = [];
        if (isset($params['status'])) {
            $new_status = sanitize_text_field($params['status']);
            // Auto deduct inventory when transitioned to 'Preparing' from 'Pending'
            if ($new_status === 'Preparing' && $order['status'] === 'Pending') {
                $this->repository->deductInventory($id);
            }
            $data['status'] = $new_status;
        }
        if (isset($params['customer_name'])) $data['customer_name'] = sanitize_text_field($params['customer_name']);
        if (isset($params['table_id'])) $data['table_id'] = intval($params['table_id']);

        if (isset($params['order_items'])) {
            $subtotal = 0.00;
            $tax = 0.00;
            foreach ($params['order_items'] as $item) {
                $qty = intval($item['quantity'] ?? 1);
                $price = floatval($item['price']);
                $item_tax_pct = floatval($item['tax_percentage'] ?? 5.00);
                
                $item_subtotal = $price * $qty;
                $item_tax = $item_subtotal * ($item_tax_pct / 100);
                
                $subtotal += $item_subtotal;
                $tax += $item_tax;
            }

            $discount = isset($params['discount']) ? floatval($params['discount']) : floatval($order['discount']);
            $total_amount = ($subtotal + $tax) - $discount;

            $data['subtotal'] = $subtotal;
            $data['tax'] = $tax;
            $data['total_amount'] = $total_amount;

            $this->repository->saveItems($id, $params['order_items']);
        } else if (isset($params['discount'])) {
            $discount = floatval($params['discount']);
            $data['discount'] = $discount;
            $data['total_amount'] = (floatval($order['subtotal']) + floatval($order['tax'])) - $discount;
        }

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update order details.');
        }

        $updated = $this->repository->find($id);
        $updated['order_items'] = $this->repository->getItems($id);

        return $this->success('Order updated successfully.', $updated);
    }

    public function deleteOrder(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $order = $this->repository->find($id);
        if (!$order) {
            return $this->error('Order not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete order.');
        }

        return $this->success('Order deleted successfully.');
    }
}
