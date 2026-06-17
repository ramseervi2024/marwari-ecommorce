<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\VehicleRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class VehicleController extends BaseController {
    private $vehicleRepository;

    public function __construct() {
        $this->vehicleRepository = new VehicleRepository();
    }

    /**
     * GET /vehicles
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'vehicle_number', 'vehicle_type', 'vehicle_model', 'status', 'created_at'];
        $search_fields = ['vehicle_number', 'vehicle_type', 'vehicle_model', 'registration_number'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['vehicle_type'])) {
            $extra_filters['vehicle_type'] = sanitize_text_field($params['vehicle_type']);
        }

        $results = $this->vehicleRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Vehicles retrieved successfully.', $results);
    }

    /**
     * GET /vehicles/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $vehicle = $this->vehicleRepository->findById($id);

        if (!$vehicle) {
            return $this->error('Vehicle not found.', [], 404);
        }

        return $this->success('Vehicle retrieved successfully.', $vehicle);
    }

    /**
     * POST /vehicles
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['vehicle_number']) || empty($params['vehicle_type'])) {
            return $this->error('Validation failed: vehicle_number and vehicle_type are required.');
        }

        $number = sanitize_text_field($params['vehicle_number']);
        if ($this->vehicleRepository->existsVehicleNumber($number)) {
            return $this->error('Validation failed: Vehicle number already exists.');
        }

        $data = [
            'vehicle_number' => $number,
            'vehicle_type' => sanitize_text_field($params['vehicle_type']),
            'vehicle_model' => sanitize_text_field($params['vehicle_model'] ?? ''),
            'registration_number' => sanitize_text_field($params['registration_number'] ?? ''),
            'insurance_expiry' => !empty($params['insurance_expiry']) ? sanitize_text_field($params['insurance_expiry']) : null,
            'permit_expiry' => !empty($params['permit_expiry']) ? sanitize_text_field($params['permit_expiry']) : null,
            'fitness_expiry' => !empty($params['fitness_expiry']) ? sanitize_text_field($params['fitness_expiry']) : null,
            'purchase_date' => !empty($params['purchase_date']) ? sanitize_text_field($params['purchase_date']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->vehicleRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create vehicle.');
        }

        AuthService::logActivity(get_current_user_id(), 'VEHICLE_CREATE', "Registered vehicle $number ($inserted_id)");

        return $this->success('Vehicle registered successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /vehicles/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $vehicle = $this->vehicleRepository->findById($id);

        if (!$vehicle) {
            return $this->error('Vehicle not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['vehicle_number', 'vehicle_type', 'vehicle_model', 'registration_number', 'insurance_expiry', 'permit_expiry', 'fitness_expiry', 'purchase_date', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'vehicle_number') {
                    $number = sanitize_text_field($params[$field]);
                    if ($this->vehicleRepository->existsVehicleNumber($number, $id)) {
                        return $this->error('Validation failed: Vehicle number already exists.');
                    }
                    $data[$field] = $number;
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = '%s';
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->vehicleRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update vehicle.');
        }

        AuthService::logActivity(get_current_user_id(), 'VEHICLE_UPDATE', "Updated vehicle ID: $id");

        return $this->success('Vehicle updated successfully.', $this->vehicleRepository->findById($id));
    }

    /**
     * DELETE /vehicles/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $vehicle = $this->vehicleRepository->findById($id);

        if (!$vehicle) {
            return $this->error('Vehicle not found.', [], 404);
        }

        $deleted = $this->vehicleRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete vehicle.');
        }

        AuthService::logActivity(get_current_user_id(), 'VEHICLE_DELETE', "Soft deleted vehicle ID: $id ($vehicle[vehicle_number])");

        return $this->success('Vehicle deleted successfully.');
    }
}
