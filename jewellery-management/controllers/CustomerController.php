<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\CustomerRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class CustomerController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new CustomerRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Customers retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Customer not found.', [], 404);
        }
        return $this->success('Customer retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['name'])) {
            return $this->error('Validation failed: name is required.');
        }

        $params['customer_code'] = sanitize_text_field($params['customer_code'] ?? 'CUST-' . rand(100000, 999999));
        $params['name'] = sanitize_text_field($params['name']);
        $params['mobile'] = sanitize_text_field($params['mobile'] ?? '');
        $params['email'] = sanitize_email($params['email'] ?? '');
        $params['address'] = sanitize_textarea_field($params['address'] ?? '');
        $params['aadhaar_number'] = sanitize_text_field($params['aadhaar_number'] ?? '');
        $params['pan_number'] = sanitize_text_field($params['pan_number'] ?? '');
        $params['loyalty_points'] = intval($params['loyalty_points'] ?? 0);
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create customer.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_CREATE', "Created customer: {$params['name']} [{$params['customer_code']}]");

        return $this->success('Customer created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Customer not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['customer_code'])) $updates['customer_code'] = sanitize_text_field($params['customer_code']);
        if (isset($params['name'])) $updates['name'] = sanitize_text_field($params['name']);
        if (isset($params['mobile'])) $updates['mobile'] = sanitize_text_field($params['mobile']);
        if (isset($params['email'])) $updates['email'] = sanitize_email($params['email']);
        if (isset($params['address'])) $updates['address'] = sanitize_textarea_field($params['address']);
        if (isset($params['aadhaar_number'])) $updates['aadhaar_number'] = sanitize_text_field($params['aadhaar_number']);
        if (isset($params['pan_number'])) $updates['pan_number'] = sanitize_text_field($params['pan_number']);
        if (isset($params['loyalty_points'])) $updates['loyalty_points'] = intval($params['loyalty_points']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update customer.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_UPDATE', "Updated customer ID $id");

        return $this->success('Customer updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Customer not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete customer.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_DELETE', "Deleted customer: {$item['name']}");

        return $this->success('Customer deleted successfully.');
    }
}
