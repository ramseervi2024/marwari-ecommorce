<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\FinishingRepository;
use WP_REST_Request;

class FinishingController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new FinishingRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Finishing items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Finishing item not found.', [], 404);
        }
        return $this->success('Finishing item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['batch_number']) || empty($params['order_id']) || empty($params['process_type']) || empty($params['quantity'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['batch_number'] = sanitize_text_field($params['batch_number'] ?? '');
        $params['order_id'] = intval($params['order_id'] ?? 0);
        $params['process_type'] = sanitize_text_field($params['process_type'] ?? '');
        $params['quantity'] = floatval($params['quantity'] ?? 0);
        $params['completed_quantity'] = floatval($params['completed_quantity'] ?? 0);
        $params['defects_found'] = floatval($params['defects_found'] ?? 0);
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Finishing item.');
        }

        return $this->success('Finishing item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Finishing item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['batch_number'])) $updates['batch_number'] = sanitize_text_field($params['batch_number']);
        if (isset($params['order_id'])) $updates['order_id'] = intval($params['order_id']);
        if (isset($params['process_type'])) $updates['process_type'] = sanitize_text_field($params['process_type']);
        if (isset($params['quantity'])) $updates['quantity'] = floatval($params['quantity']);
        if (isset($params['completed_quantity'])) $updates['completed_quantity'] = floatval($params['completed_quantity']);
        if (isset($params['defects_found'])) $updates['defects_found'] = floatval($params['defects_found']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Finishing item.');
        }

        return $this->success('Finishing item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Finishing item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Finishing item.');
        }
        return $this->success('Finishing item deleted successfully.');
    }
}
