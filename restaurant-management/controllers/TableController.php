<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\TableRepository;
use WP_REST_Request;

class TableController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new TableRepository();
    }

    public function getTables(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $tables = $this->repository->all($limit, $offset);
        return $this->success('Tables retrieved successfully.', $tables);
    }

    public function createTable(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['table_number'])) {
            return $this->error('Validation failed: table_number is required.');
        }

        $data = [
            'table_number' => sanitize_text_field($params['table_number']),
            'capacity' => intval($params['capacity'] ?? 4),
            'floor' => sanitize_text_field($params['floor'] ?? 'Ground'),
            'status' => sanitize_text_field($params['status'] ?? 'Available')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to create table.');
        }

        $data['id'] = $id;
        return $this->success('Table created successfully.', $data, 201);
    }

    public function updateTable(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $table = $this->repository->find($id);
        if (!$table) {
            return $this->error('Table not found.', [], 404);
        }

        $data = [];
        if (isset($params['table_number'])) $data['table_number'] = sanitize_text_field($params['table_number']);
        if (isset($params['capacity'])) $data['capacity'] = intval($params['capacity']);
        if (isset($params['floor'])) $data['floor'] = sanitize_text_field($params['floor']);
        if (isset($params['status'])) $data['status'] = sanitize_text_field($params['status']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update table.');
        }

        return $this->success('Table updated successfully.', array_merge($table, $data));
    }

    public function deleteTable(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $table = $this->repository->find($id);
        if (!$table) {
            return $this->error('Table not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete table.');
        }

        return $this->success('Table deleted successfully.');
    }
}
