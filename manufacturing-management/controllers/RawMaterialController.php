<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\RawMaterialRepository;
use WP_REST_Request;

class RawMaterialController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new RawMaterialRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Raw materials retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Raw material not found.', [], 404);
        }
        return $this->success('Raw material retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['material_code']) || empty($params['material_name'])) {
            return $this->error('Validation failed: material_code and material_name are required.');
        }

        $params['minimum_stock'] = floatval($params['minimum_stock'] ?? 0);
        $params['current_stock'] = floatval($params['current_stock'] ?? 0);
        $params['purchase_price'] = floatval($params['purchase_price'] ?? 0);
        $params['supplier_id'] = !empty($params['supplier_id']) ? intval($params['supplier_id']) : null;
        $params['status'] = sanitize_text_field($params['status'] ?? 'ACTIVE');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create raw material. Ensure material_code is unique.');
        }

        return $this->success('Raw material created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Raw material not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['material_name'])) $updates['material_name'] = sanitize_text_field($params['material_name']);
        if (isset($params['category'])) $updates['category'] = sanitize_text_field($params['category']);
        if (isset($params['unit'])) $updates['unit'] = sanitize_text_field($params['unit']);
        if (isset($params['minimum_stock'])) $updates['minimum_stock'] = floatval($params['minimum_stock']);
        if (isset($params['current_stock'])) $updates['current_stock'] = floatval($params['current_stock']);
        if (isset($params['purchase_price'])) $updates['purchase_price'] = floatval($params['purchase_price']);
        if (isset($params['supplier_id'])) $updates['supplier_id'] = intval($params['supplier_id']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update raw material.');
        }

        return $this->success('Raw material updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Raw material not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete raw material.');
        }
        return $this->success('Raw material deleted successfully.');
    }
}
