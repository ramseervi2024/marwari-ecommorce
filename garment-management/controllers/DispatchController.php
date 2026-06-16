<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\DispatchRepository;
use WP_REST_Request;

class DispatchController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new DispatchRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Dispatch items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Dispatch item not found.', [], 404);
        }
        return $this->success('Dispatch item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['dispatch_number']) || empty($params['order_id']) || empty($params['quantity'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['dispatch_number'] = sanitize_text_field($params['dispatch_number'] ?? '');
        $params['order_id'] = intval($params['order_id'] ?? 0);
        $params['customer_name'] = sanitize_text_field($params['customer_name'] ?? '');
        $params['quantity'] = floatval($params['quantity'] ?? 0);
        $params['transport_company'] = sanitize_text_field($params['transport_company'] ?? '');
        $params['tracking_number'] = sanitize_text_field($params['tracking_number'] ?? '');
        $params['dispatch_date'] = sanitize_text_field($params['dispatch_date'] ?? '');
        $params['delivery_date'] = sanitize_text_field($params['delivery_date'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Dispatch item.');
        }

        return $this->success('Dispatch item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Dispatch item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['dispatch_number'])) $updates['dispatch_number'] = sanitize_text_field($params['dispatch_number']);
        if (isset($params['order_id'])) $updates['order_id'] = intval($params['order_id']);
        if (isset($params['customer_name'])) $updates['customer_name'] = sanitize_text_field($params['customer_name']);
        if (isset($params['quantity'])) $updates['quantity'] = floatval($params['quantity']);
        if (isset($params['transport_company'])) $updates['transport_company'] = sanitize_text_field($params['transport_company']);
        if (isset($params['tracking_number'])) $updates['tracking_number'] = sanitize_text_field($params['tracking_number']);
        if (isset($params['dispatch_date'])) $updates['dispatch_date'] = sanitize_text_field($params['dispatch_date']);
        if (isset($params['delivery_date'])) $updates['delivery_date'] = sanitize_text_field($params['delivery_date']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Dispatch item.');
        }

        return $this->success('Dispatch item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Dispatch item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Dispatch item.');
        }
        return $this->success('Dispatch item deleted successfully.');
    }
}
