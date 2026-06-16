<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\CustomerRepository;
use WP_REST_Request;

class CustomerController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new CustomerRepository();
    }

    public function getCustomers(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $customers = $this->repository->all($limit, $offset);
        return $this->success('Customers retrieved successfully.', $customers);
    }

    public function createCustomer(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['name']) || empty($params['mobile'])) {
            return $this->error('Validation failed: name and mobile are required.');
        }

        $existing = $this->repository->findByMobile($params['mobile']);
        if ($existing) {
            return $this->success('Customer already exists.', $existing);
        }

        $data = [
            'name' => sanitize_text_field($params['name']),
            'mobile' => sanitize_text_field($params['mobile']),
            'email' => sanitize_email($params['email'] ?? ''),
            'address' => sanitize_text_field($params['address'] ?? ''),
            'loyalty_points' => intval($params['loyalty_points'] ?? 0)
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to register customer.');
        }

        $data['id'] = $id;
        return $this->success('Customer registered successfully.', $data, 201);
    }

    public function updateCustomer(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $customer = $this->repository->find($id);
        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        $data = [];
        if (isset($params['name'])) $data['name'] = sanitize_text_field($params['name']);
        if (isset($params['mobile'])) $data['mobile'] = sanitize_text_field($params['mobile']);
        if (isset($params['email'])) $data['email'] = sanitize_email($params['email']);
        if (isset($params['address'])) $data['address'] = sanitize_text_field($params['address']);
        if (isset($params['loyalty_points'])) $data['loyalty_points'] = intval($params['loyalty_points']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update customer details.');
        }

        return $this->success('Customer updated successfully.', array_merge($customer, $data));
    }

    public function deleteCustomer(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->repository->find($id);
        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete customer.');
        }

        return $this->success('Customer profile deleted successfully.');
    }

    public function redeemLoyaltyPoints(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['customer_id']) || empty($params['points'])) {
            return $this->error('Validation failed: customer_id and points are required.');
        }

        $customer_id = intval($params['customer_id']);
        $points = intval($params['points']);

        $customer = $this->repository->find($customer_id);
        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        if (intval($customer['loyalty_points']) < $points) {
            return $this->error('Insufficient loyalty points balance.');
        }

        global $wpdb;
        $t_customers = $wpdb->prefix . 'restaurant_customers';
        $wpdb->query($wpdb->prepare(
            "UPDATE {$t_customers} 
             SET loyalty_points = loyalty_points - %d 
             WHERE id = %d",
            $points,
            $customer_id
        ));

        // 1 point = 0.5 Rupees/Dollars discount
        $discount_value = $points * 0.5;

        return $this->success('Loyalty points redeemed successfully.', [
            'points_redeemed' => $points,
            'discount_value' => $discount_value,
            'remaining_points' => intval($customer['loyalty_points']) - $points
        ]);
    }
}
