<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\WarehouseRepository;
use WP_REST_Request;

class WarehouseController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new WarehouseRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        return $this->success('Warehouses retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['warehouse_name'])) {
            return $this->error('Validation failed: warehouse_name is required.');
        }

        $params['location'] = sanitize_text_field($params['location'] ?? '');
        $params['manager'] = sanitize_text_field($params['manager'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? 'ACTIVE');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create warehouse.');
        }

        return $this->success('Warehouse created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Warehouse not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['warehouse_name'])) $updates['warehouse_name'] = sanitize_text_field($params['warehouse_name']);
        if (isset($params['location'])) $updates['location'] = sanitize_text_field($params['location']);
        if (isset($params['manager'])) $updates['manager'] = sanitize_text_field($params['manager']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update warehouse.');
        }

        return $this->success('Warehouse updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Warehouse not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete warehouse.');
        }
        return $this->success('Warehouse deleted successfully.');
    }
}
