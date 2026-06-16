<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\MachineRepository;
use WP_REST_Request;

class MachineController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new MachineRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        return $this->success('Machines retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['machine_code']) || empty($params['machine_name'])) {
            return $this->error('Validation failed: machine_code and machine_name are required.');
        }

        $params['capacity'] = sanitize_text_field($params['capacity'] ?? '');
        $params['maintenance_due'] = !empty($params['maintenance_due']) ? sanitize_text_field($params['maintenance_due']) : null;
        $params['status'] = sanitize_text_field($params['status'] ?? 'ACTIVE');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create machine. Ensure machine_code is unique.');
        }

        return $this->success('Machine created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Machine not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['machine_name'])) $updates['machine_name'] = sanitize_text_field($params['machine_name']);
        if (isset($params['capacity'])) $updates['capacity'] = sanitize_text_field($params['capacity']);
        if (isset($params['maintenance_due'])) $updates['maintenance_due'] = sanitize_text_field($params['maintenance_due']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update machine.');
        }

        return $this->success('Machine updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Machine not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete machine.');
        }
        return $this->success('Machine deleted successfully.');
    }
}
