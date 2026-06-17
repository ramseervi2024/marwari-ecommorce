<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\WarehouseRepository;
use InventoryManagementApi\Services\AuthService;
use WP_REST_Request;

class WarehouseController extends BaseController {
    private $warehouseRepository;

    public function __construct() {
        $this->warehouseRepository = new WarehouseRepository();
    }

    /**
     * GET /warehouses
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'warehouse_code', 'warehouse_name', 'capacity', 'created_at'];
        $search_fields = ['warehouse_code', 'warehouse_name', 'location', 'manager_name'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->warehouseRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Warehouses list retrieved successfully.', $results);
    }

    /**
     * GET /warehouses/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $warehouse = $this->warehouseRepository->findById($id);

        if (!$warehouse) {
            return $this->error('Warehouse not found.', [], 404);
        }

        return $this->success('Warehouse retrieved successfully.', $warehouse);
    }

    /**
     * POST /warehouses
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['warehouse_name']) || empty($params['warehouse_code'])) {
            return $this->error('Validation failed: warehouse_name and warehouse_code are required.');
        }

        if ($this->warehouseRepository->existsWarehouseCode($params['warehouse_code'])) {
            return $this->error('Warehouse code already exists.');
        }

        $data = [
            'warehouse_code' => sanitize_text_field($params['warehouse_code']),
            'warehouse_name' => sanitize_text_field($params['warehouse_name']),
            'location' => sanitize_textarea_field($params['location'] ?? ''),
            'manager_name' => sanitize_text_field($params['manager_name'] ?? ''),
            'contact_number' => sanitize_text_field($params['contact_number'] ?? ''),
            'capacity' => intval($params['capacity'] ?? 10000),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%d', '%s'];
        $inserted_id = $this->warehouseRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create warehouse.');
        }

        AuthService::logActivity(get_current_user_id(), 'WAREHOUSE_CREATE', "Created warehouse {$data['warehouse_code']} - {$data['warehouse_name']}");

        return $this->success('Warehouse created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /warehouses/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $warehouse = $this->warehouseRepository->findById($id);

        if (!$warehouse) {
            return $this->error('Warehouse not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['warehouse_code'])) {
            if ($this->warehouseRepository->existsWarehouseCode($params['warehouse_code'], $id)) {
                return $this->error('Warehouse code already in use.');
            }
            $data['warehouse_code'] = sanitize_text_field($params['warehouse_code']);
            $formats[] = '%s';
        }

        $fields = [
            'warehouse_name' => '%s',
            'location' => '%s',
            'manager_name' => '%s',
            'contact_number' => '%s',
            'capacity' => '%d',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $data[$field] = intval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->warehouseRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update warehouse details.');
        }

        AuthService::logActivity(get_current_user_id(), 'WAREHOUSE_UPDATE', "Updated warehouse ID: $id");

        return $this->success('Warehouse updated successfully.', $this->warehouseRepository->findById($id));
    }

    /**
     * DELETE /warehouses/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $warehouse = $this->warehouseRepository->findById($id);

        if (!$warehouse) {
            return $this->error('Warehouse not found.', [], 404);
        }

        $deleted = $this->warehouseRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete warehouse.');
        }

        AuthService::logActivity(get_current_user_id(), 'WAREHOUSE_DELETE', "Soft deleted warehouse ID: $id ({$warehouse['warehouse_code']})");

        return $this->success('Warehouse deleted successfully.');
    }
}
