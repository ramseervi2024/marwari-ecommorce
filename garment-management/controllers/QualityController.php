<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\QualityRepository;
use WP_REST_Request;

class QualityController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new QualityRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Quality items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Quality item not found.', [], 404);
        }
        return $this->success('Quality item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['inspection_number']) || empty($params['order_id']) || empty($params['batch_number']) || empty($params['approved_quantity']) || empty($params['rejected_quantity'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['inspection_number'] = sanitize_text_field($params['inspection_number'] ?? '');
        $params['order_id'] = intval($params['order_id'] ?? 0);
        $params['batch_number'] = sanitize_text_field($params['batch_number'] ?? '');
        $params['approved_quantity'] = floatval($params['approved_quantity'] ?? 0);
        $params['rejected_quantity'] = floatval($params['rejected_quantity'] ?? 0);
        $params['defect_type'] = sanitize_text_field($params['defect_type'] ?? '');
        $params['remarks'] = sanitize_textarea_field($params['remarks'] ?? '');
        $params['inspection_date'] = sanitize_text_field($params['inspection_date'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Quality item.');
        }

        return $this->success('Quality item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Quality item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['inspection_number'])) $updates['inspection_number'] = sanitize_text_field($params['inspection_number']);
        if (isset($params['order_id'])) $updates['order_id'] = intval($params['order_id']);
        if (isset($params['batch_number'])) $updates['batch_number'] = sanitize_text_field($params['batch_number']);
        if (isset($params['approved_quantity'])) $updates['approved_quantity'] = floatval($params['approved_quantity']);
        if (isset($params['rejected_quantity'])) $updates['rejected_quantity'] = floatval($params['rejected_quantity']);
        if (isset($params['defect_type'])) $updates['defect_type'] = sanitize_text_field($params['defect_type']);
        if (isset($params['remarks'])) $updates['remarks'] = sanitize_textarea_field($params['remarks']);
        if (isset($params['inspection_date'])) $updates['inspection_date'] = sanitize_text_field($params['inspection_date']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Quality item.');
        }

        return $this->success('Quality item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Quality item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Quality item.');
        }
        return $this->success('Quality item deleted successfully.');
    }
}
