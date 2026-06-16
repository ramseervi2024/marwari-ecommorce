<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\MachineRepository;
use WP_REST_Request;

class MachineController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new MachineRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Machine items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Machine item not found.', [], 404);
        }
        return $this->success('Machine item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['machine_code']) || empty($params['machine_name'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['machine_code'] = sanitize_text_field($params['machine_code'] ?? '');
        $params['machine_name'] = sanitize_text_field($params['machine_name'] ?? '');
        $params['machine_type'] = sanitize_text_field($params['machine_type'] ?? '');
        $params['department'] = sanitize_text_field($params['department'] ?? '');
        $params['maintenance_due'] = sanitize_text_field($params['maintenance_due'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Machine item.');
        }

        return $this->success('Machine item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Machine item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['machine_code'])) $updates['machine_code'] = sanitize_text_field($params['machine_code']);
        if (isset($params['machine_name'])) $updates['machine_name'] = sanitize_text_field($params['machine_name']);
        if (isset($params['machine_type'])) $updates['machine_type'] = sanitize_text_field($params['machine_type']);
        if (isset($params['department'])) $updates['department'] = sanitize_text_field($params['department']);
        if (isset($params['maintenance_due'])) $updates['maintenance_due'] = sanitize_text_field($params['maintenance_due']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Machine item.');
        }

        return $this->success('Machine item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Machine item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Machine item.');
        }
        return $this->success('Machine item deleted successfully.');
    }
}
