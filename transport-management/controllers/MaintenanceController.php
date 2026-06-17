<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\MaintenanceRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class MaintenanceController extends BaseController {
    private $maintenanceRepository;

    public function __construct() {
        $this->maintenanceRepository = new MaintenanceRepository();
    }

    /**
     * GET /maintenance
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'vehicle_id', 'maintenance_type', 'cost', 'service_date', 'status', 'created_at'];
        $search_fields = ['maintenance_type', 'description', 'service_center'];
        
        $extra_filters = [];
        if (isset($params['vehicle_id'])) {
            $extra_filters['vehicle_id'] = intval($params['vehicle_id']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->maintenanceRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Maintenance records retrieved successfully.', $results);
    }

    /**
     * GET /maintenance/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $record = $this->maintenanceRepository->findById($id);

        if (!$record) {
            return $this->error('Maintenance record not found.', [], 404);
        }

        return $this->success('Maintenance record retrieved successfully.', $record);
    }

    /**
     * POST /maintenance
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['vehicle_id']) || empty($params['maintenance_type']) || empty($params['cost'])) {
            return $this->error('Validation failed: vehicle_id, maintenance_type, and cost are required.');
        }

        $data = [
            'vehicle_id' => intval($params['vehicle_id']),
            'maintenance_type' => sanitize_text_field($params['maintenance_type']),
            'description' => sanitize_text_field($params['description'] ?? ''),
            'service_center' => sanitize_text_field($params['service_center'] ?? ''),
            'cost' => floatval($params['cost']),
            'service_date' => !empty($params['service_date']) ? sanitize_text_field($params['service_date']) : date('Y-m-d'),
            'next_service_date' => !empty($params['next_service_date']) ? sanitize_text_field($params['next_service_date']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'Scheduled')
        ];

        $formats = ['%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s'];
        $inserted_id = $this->maintenanceRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to log maintenance record.');
        }

        AuthService::logActivity(get_current_user_id(), 'MAINTENANCE_CREATE', "Scheduled maintenance ({$data['maintenance_type']}) costing ₹{$data['cost']} for vehicle ID {$data['vehicle_id']}");

        return $this->success('Maintenance record scheduled successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /maintenance/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $record = $this->maintenanceRepository->findById($id);

        if (!$record) {
            return $this->error('Maintenance record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'vehicle_id' => '%d',
            'maintenance_type' => '%s',
            'description' => '%s',
            'service_center' => '%s',
            'cost' => '%f',
            'service_date' => '%s',
            'next_service_date' => '%s',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $data[$field] = intval($params[$field]);
                } elseif ($format === '%f') {
                    $data[$field] = floatval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->maintenanceRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update maintenance record.');
        }

        AuthService::logActivity(get_current_user_id(), 'MAINTENANCE_UPDATE', "Updated maintenance record ID: $id");

        return $this->success('Maintenance record updated successfully.', $this->maintenanceRepository->findById($id));
    }

    /**
     * DELETE /maintenance/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $record = $this->maintenanceRepository->findById($id);

        if (!$record) {
            return $this->error('Maintenance record not found.', [], 404);
        }

        $deleted = $this->maintenanceRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete maintenance record.');
        }

        AuthService::logActivity(get_current_user_id(), 'MAINTENANCE_DELETE', "Soft deleted maintenance log ID: $id");

        return $this->success('Maintenance record deleted successfully.');
    }
}
