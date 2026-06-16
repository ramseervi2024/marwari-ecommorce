<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\ProductionPlanRepository;
use WP_REST_Request;

class ProductionPlanController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new ProductionPlanRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('ProductionPlan items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('ProductionPlan item not found.', [], 404);
        }
        return $this->success('ProductionPlan item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['plan_number']) || empty($params['order_id']) || empty($params['planned_quantity'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['plan_number'] = sanitize_text_field($params['plan_number'] ?? '');
        $params['order_id'] = intval($params['order_id'] ?? 0);
        $params['planned_quantity'] = floatval($params['planned_quantity'] ?? 0);
        $params['start_date'] = sanitize_text_field($params['start_date'] ?? '');
        $params['end_date'] = sanitize_text_field($params['end_date'] ?? '');
        $params['priority'] = sanitize_text_field($params['priority'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create ProductionPlan item.');
        }

        return $this->success('ProductionPlan item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('ProductionPlan item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['plan_number'])) $updates['plan_number'] = sanitize_text_field($params['plan_number']);
        if (isset($params['order_id'])) $updates['order_id'] = intval($params['order_id']);
        if (isset($params['planned_quantity'])) $updates['planned_quantity'] = floatval($params['planned_quantity']);
        if (isset($params['start_date'])) $updates['start_date'] = sanitize_text_field($params['start_date']);
        if (isset($params['end_date'])) $updates['end_date'] = sanitize_text_field($params['end_date']);
        if (isset($params['priority'])) $updates['priority'] = sanitize_text_field($params['priority']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update ProductionPlan item.');
        }

        return $this->success('ProductionPlan item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('ProductionPlan item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete ProductionPlan item.');
        }
        return $this->success('ProductionPlan item deleted successfully.');
    }
}
