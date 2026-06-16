<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\AccessoryRepository;
use WP_REST_Request;

class AccessoryController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new AccessoryRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Accessory items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Accessory item not found.', [], 404);
        }
        return $this->success('Accessory item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['accessory_name']) || empty($params['category'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['accessory_name'] = sanitize_text_field($params['accessory_name'] ?? '');
        $params['category'] = sanitize_text_field($params['category'] ?? '');
        $params['available_quantity'] = floatval($params['available_quantity'] ?? 0);
        $params['unit'] = sanitize_text_field($params['unit'] ?? '');
        $params['cost_per_unit'] = floatval($params['cost_per_unit'] ?? 0);
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Accessory item.');
        }

        return $this->success('Accessory item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Accessory item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['accessory_name'])) $updates['accessory_name'] = sanitize_text_field($params['accessory_name']);
        if (isset($params['category'])) $updates['category'] = sanitize_text_field($params['category']);
        if (isset($params['available_quantity'])) $updates['available_quantity'] = floatval($params['available_quantity']);
        if (isset($params['unit'])) $updates['unit'] = sanitize_text_field($params['unit']);
        if (isset($params['cost_per_unit'])) $updates['cost_per_unit'] = floatval($params['cost_per_unit']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Accessory item.');
        }

        return $this->success('Accessory item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Accessory item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Accessory item.');
        }
        return $this->success('Accessory item deleted successfully.');
    }
}
