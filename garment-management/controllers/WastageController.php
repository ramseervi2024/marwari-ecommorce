<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\WastageRepository;
use WP_REST_Request;

class WastageController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new WastageRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Wastage items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Wastage item not found.', [], 404);
        }
        return $this->success('Wastage item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['department']) || empty($params['material_type']) || empty($params['quantity'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['department'] = sanitize_text_field($params['department'] ?? '');
        $params['material_type'] = sanitize_text_field($params['material_type'] ?? '');
        $params['quantity'] = floatval($params['quantity'] ?? 0);
        $params['reason'] = sanitize_text_field($params['reason'] ?? '');
        $params['cost_impact'] = floatval($params['cost_impact'] ?? 0);
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Wastage item.');
        }

        return $this->success('Wastage item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Wastage item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['department'])) $updates['department'] = sanitize_text_field($params['department']);
        if (isset($params['material_type'])) $updates['material_type'] = sanitize_text_field($params['material_type']);
        if (isset($params['quantity'])) $updates['quantity'] = floatval($params['quantity']);
        if (isset($params['reason'])) $updates['reason'] = sanitize_text_field($params['reason']);
        if (isset($params['cost_impact'])) $updates['cost_impact'] = floatval($params['cost_impact']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Wastage item.');
        }

        return $this->success('Wastage item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Wastage item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Wastage item.');
        }
        return $this->success('Wastage item deleted successfully.');
    }
}
