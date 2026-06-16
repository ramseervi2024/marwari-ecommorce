<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\FabricRepository;
use WP_REST_Request;

class FabricController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new FabricRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Fabric items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Fabric item not found.', [], 404);
        }
        return $this->success('Fabric item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['fabric_code']) || empty($params['fabric_name'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['fabric_code'] = sanitize_text_field($params['fabric_code'] ?? '');
        $params['fabric_name'] = sanitize_text_field($params['fabric_name'] ?? '');
        $params['fabric_type'] = sanitize_text_field($params['fabric_type'] ?? '');
        $params['color'] = sanitize_text_field($params['color'] ?? '');
        $params['gsm'] = intval($params['gsm'] ?? 0);
        $params['width'] = floatval($params['width'] ?? 0);
        $params['available_meters'] = floatval($params['available_meters'] ?? 0);
        $params['cost_per_meter'] = floatval($params['cost_per_meter'] ?? 0);
        $params['supplier_id'] = intval($params['supplier_id'] ?? 0);
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Fabric item.');
        }

        return $this->success('Fabric item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Fabric item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['fabric_code'])) $updates['fabric_code'] = sanitize_text_field($params['fabric_code']);
        if (isset($params['fabric_name'])) $updates['fabric_name'] = sanitize_text_field($params['fabric_name']);
        if (isset($params['fabric_type'])) $updates['fabric_type'] = sanitize_text_field($params['fabric_type']);
        if (isset($params['color'])) $updates['color'] = sanitize_text_field($params['color']);
        if (isset($params['gsm'])) $updates['gsm'] = intval($params['gsm']);
        if (isset($params['width'])) $updates['width'] = floatval($params['width']);
        if (isset($params['available_meters'])) $updates['available_meters'] = floatval($params['available_meters']);
        if (isset($params['cost_per_meter'])) $updates['cost_per_meter'] = floatval($params['cost_per_meter']);
        if (isset($params['supplier_id'])) $updates['supplier_id'] = intval($params['supplier_id']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Fabric item.');
        }

        return $this->success('Fabric item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Fabric item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Fabric item.');
        }
        return $this->success('Fabric item deleted successfully.');
    }
}
