<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\OrderRepository;
use WP_REST_Request;

class OrderController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new OrderRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Order items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Order item not found.', [], 404);
        }
        return $this->success('Order item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['order_number']) || empty($params['customer_name']) || empty($params['product_name'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['order_number'] = sanitize_text_field($params['order_number'] ?? '');
        $params['customer_name'] = sanitize_text_field($params['customer_name'] ?? '');
        $params['product_name'] = sanitize_text_field($params['product_name'] ?? '');
        $params['style_code'] = sanitize_text_field($params['style_code'] ?? '');
        $params['quantity'] = floatval($params['quantity'] ?? 0);
        $params['unit_price'] = floatval($params['unit_price'] ?? 0);
        $params['delivery_date'] = sanitize_text_field($params['delivery_date'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Order item.');
        }

        return $this->success('Order item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Order item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['order_number'])) $updates['order_number'] = sanitize_text_field($params['order_number']);
        if (isset($params['customer_name'])) $updates['customer_name'] = sanitize_text_field($params['customer_name']);
        if (isset($params['product_name'])) $updates['product_name'] = sanitize_text_field($params['product_name']);
        if (isset($params['style_code'])) $updates['style_code'] = sanitize_text_field($params['style_code']);
        if (isset($params['quantity'])) $updates['quantity'] = floatval($params['quantity']);
        if (isset($params['unit_price'])) $updates['unit_price'] = floatval($params['unit_price']);
        if (isset($params['delivery_date'])) $updates['delivery_date'] = sanitize_text_field($params['delivery_date']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Order item.');
        }

        return $this->success('Order item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Order item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Order item.');
        }
        return $this->success('Order item deleted successfully.');
    }
}
