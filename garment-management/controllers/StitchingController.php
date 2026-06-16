<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\StitchingRepository;
use WP_REST_Request;

class StitchingController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new StitchingRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Stitching items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Stitching item not found.', [], 404);
        }
        return $this->success('Stitching item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['production_batch']) || empty($params['order_id']) || empty($params['worker_id']) || empty($params['target_quantity'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['production_batch'] = sanitize_text_field($params['production_batch'] ?? '');
        $params['order_id'] = intval($params['order_id'] ?? 0);
        $params['worker_id'] = intval($params['worker_id'] ?? 0);
        $params['machine_id'] = intval($params['machine_id'] ?? 0);
        $params['target_quantity'] = floatval($params['target_quantity'] ?? 0);
        $params['completed_quantity'] = floatval($params['completed_quantity'] ?? 0);
        $params['rejected_quantity'] = floatval($params['rejected_quantity'] ?? 0);
        $params['production_date'] = sanitize_text_field($params['production_date'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Stitching item.');
        }

        return $this->success('Stitching item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Stitching item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['production_batch'])) $updates['production_batch'] = sanitize_text_field($params['production_batch']);
        if (isset($params['order_id'])) $updates['order_id'] = intval($params['order_id']);
        if (isset($params['worker_id'])) $updates['worker_id'] = intval($params['worker_id']);
        if (isset($params['machine_id'])) $updates['machine_id'] = intval($params['machine_id']);
        if (isset($params['target_quantity'])) $updates['target_quantity'] = floatval($params['target_quantity']);
        if (isset($params['completed_quantity'])) $updates['completed_quantity'] = floatval($params['completed_quantity']);
        if (isset($params['rejected_quantity'])) $updates['rejected_quantity'] = floatval($params['rejected_quantity']);
        if (isset($params['production_date'])) $updates['production_date'] = sanitize_text_field($params['production_date']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Stitching item.');
        }

        return $this->success('Stitching item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Stitching item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Stitching item.');
        }
        return $this->success('Stitching item deleted successfully.');
    }
}
