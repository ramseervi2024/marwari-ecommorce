<?php
namespace ConstructionManagementApi\Controllers;

use ConstructionManagementApi\Repositories\EquipmentRepository;
use ConstructionManagementApi\Services\AuthService;
use WP_REST_Request;

class EquipmentController extends BaseController {
    private $equipmentRepository;

    public function __construct() {
        $this->equipmentRepository = new EquipmentRepository();
    }

    /**
     * GET /equipment
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'equipment_code', 'equipment_name', 'purchase_cost', 'rental_cost', 'status', 'maintenance_due'];
        $search_fields = ['equipment_code', 'equipment_name', 'location', 'status'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['location'])) {
            $extra_filters['location'] = sanitize_text_field($params['location']);
        }

        $results = $this->equipmentRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Equipment assets list retrieved successfully.', $results);
    }

    /**
     * GET /equipment/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $equipment = $this->equipmentRepository->findById($id);

        if (!$equipment) {
            return $this->error('Equipment asset not found.', [], 404);
        }

        return $this->success('Equipment asset retrieved successfully.', $equipment);
    }

    /**
     * POST /equipment
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['equipment_name'])) {
            return $this->error('Validation failed: equipment_name is required.');
        }

        $equipment_code = 'EQP-' . strtoupper(substr(sanitize_key($params['equipment_name']), 0, 3)) . '-' . sprintf('%02d', rand(10, 99));
        while ($this->equipmentRepository->existsEquipmentCode($equipment_code)) {
            $equipment_code = 'EQP-' . strtoupper(substr(sanitize_key($params['equipment_name']), 0, 3)) . '-' . sprintf('%02d', rand(10, 99));
        }

        $data = [
            'equipment_code' => $equipment_code,
            'equipment_name' => sanitize_text_field($params['equipment_name']),
            'purchase_cost' => isset($params['purchase_cost']) ? floatval($params['purchase_cost']) : 0.00,
            'rental_cost' => isset($params['rental_cost']) ? floatval($params['rental_cost']) : 0.00,
            'location' => sanitize_text_field($params['location'] ?? ''),
            'maintenance_due' => !empty($params['maintenance_due']) ? sanitize_text_field($params['maintenance_due']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'Available')
        ];

        $formats = ['%s', '%s', '%f', '%f', '%s', '%s', '%s'];
        $inserted_id = $this->equipmentRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create equipment asset.');
        }

        AuthService::logActivity(get_current_user_id(), 'EQUIPMENT_CREATE', "Created equipment code $equipment_code ($inserted_id)");

        return $this->success('Equipment asset created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /equipment/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $equipment = $this->equipmentRepository->findById($id);

        if (!$equipment) {
            return $this->error('Equipment asset not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['equipment_name', 'purchase_cost', 'rental_cost', 'location', 'maintenance_due', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'purchase_cost' || $field === 'rental_cost') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->equipmentRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update equipment asset.');
        }

        AuthService::logActivity(get_current_user_id(), 'EQUIPMENT_UPDATE', "Updated equipment asset ID: $id");

        return $this->success('Equipment asset updated successfully.', $this->equipmentRepository->findById($id));
    }

    /**
     * DELETE /equipment/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $equipment = $this->equipmentRepository->findById($id);

        if (!$equipment) {
            return $this->error('Equipment asset not found.', [], 404);
        }

        $deleted = $this->equipmentRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete equipment asset.');
        }

        AuthService::logActivity(get_current_user_id(), 'EQUIPMENT_DELETE', "Soft deleted equipment ID: $id ($equipment[equipment_code])");

        return $this->success('Equipment asset deleted successfully.');
    }
}
