<?php
namespace FleetTrackPro\Controllers;

use FleetTrackPro\Repositories\VehicleRepository;
use FleetTrackPro\Services\AuthService;
use WP_REST_Request;

class VehicleController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new VehicleRepository();
    }

    /**
     * GET /vehicles
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'vehicle_number', 'vehicle_type', 'vehicle_brand', 'vehicle_year', 'status'];
        $search_fields = ['vehicle_number', 'vehicle_brand', 'vehicle_model', 'vehicle_type', 'fuel_type'];
        
        $extra_filters = [];
        if (!empty($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (!empty($params['fuel_type'])) {
            $extra_filters['fuel_type'] = sanitize_text_field($params['fuel_type']);
        }

        $result = $this->repository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Vehicles fetched successfully', $result);
    }

    /**
     * GET /vehicles/{id}
     */
    public function show(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $vehicle = $this->repository->findById($id);

        if (!$vehicle) {
            return $this->error('Vehicle not found.', [], 404);
        }

        return $this->success('Vehicle details fetched successfully', $vehicle);
    }

    /**
     * POST /vehicles
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['vehicle_number']) || empty($params['vehicle_type']) || empty($params['vehicle_brand']) || empty($params['vehicle_model'])) {
            return $this->error('Validation failed: vehicle_number, vehicle_type, vehicle_brand, vehicle_model are required.');
        }

        $vehicle_num = sanitize_text_field($params['vehicle_number']);
        if ($this->repository->exists($vehicle_num)) {
            return $this->error("Vehicle number '$vehicle_num' already exists.");
        }

        $data = [
            'vehicle_number' => $vehicle_num,
            'vehicle_type' => sanitize_text_field($params['vehicle_type']),
            'vehicle_brand' => sanitize_text_field($params['vehicle_brand']),
            'vehicle_model' => sanitize_text_field($params['vehicle_model']),
            'vehicle_year' => (int)$params['vehicle_year'],
            'fuel_type' => sanitize_text_field($params['fuel_type'] ?? 'Diesel'),
            'capacity' => sanitize_text_field($params['capacity'] ?? ''),
            'insurance_expiry' => !empty($params['insurance_expiry']) ? sanitize_text_field($params['insurance_expiry']) : null,
            'fitness_expiry' => !empty($params['fitness_expiry']) ? sanitize_text_field($params['fitness_expiry']) : null,
            'permit_expiry' => !empty($params['permit_expiry']) ? sanitize_text_field($params['permit_expiry']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $vehicle_id = $this->repository->create($data, $formats);

        if (!$vehicle_id) {
            return $this->error('Failed to create vehicle.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_VEHICLE', "Created vehicle $vehicle_num (ID: $vehicle_id)");

        return $this->success('Vehicle created successfully', array_merge(['id' => $vehicle_id], $data), 201);
    }

    /**
     * PUT /vehicles/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $vehicle = $this->repository->findById($id);

        if (!$vehicle) {
            return $this->error('Vehicle not found.', [], 404);
        }

        $params = $request->get_json_params();
        $update_data = [];
        $formats = [];

        if (isset($params['vehicle_number'])) {
            $vehicle_num = sanitize_text_field($params['vehicle_number']);
            if ($this->repository->exists($vehicle_num, $id)) {
                return $this->error("Vehicle number '$vehicle_num' already exists.");
            }
            $update_data['vehicle_number'] = $vehicle_num;
            $formats[] = '%s';
        }

        $allowed_fields = [
            'vehicle_type' => '%s',
            'vehicle_brand' => '%s',
            'vehicle_model' => '%s',
            'vehicle_year' => '%d',
            'fuel_type' => '%s',
            'capacity' => '%s',
            'insurance_expiry' => '%s',
            'fitness_expiry' => '%s',
            'permit_expiry' => '%s',
            'status' => '%s'
        ];

        foreach ($allowed_fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $update_data[$field] = (int)$params[$field];
                } else {
                    $update_data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($update_data)) {
            return $this->error('No parameters provided for update.');
        }

        $update_data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->repository->update($id, $update_data, $formats);

        if (!$success) {
            return $this->error('Failed to update vehicle details.');
        }

        AuthService::logActivity(get_current_user_id(), 'UPDATE_VEHICLE', "Updated vehicle ID: $id");

        return $this->success('Vehicle updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /vehicles/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $vehicle = $this->repository->findById($id);

        if (!$vehicle) {
            return $this->error('Vehicle not found.', [], 404);
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            return $this->error('Failed to delete vehicle.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELETE_VEHICLE', "Soft deleted vehicle ID: $id");

        return $this->success('Vehicle soft deleted successfully');
    }
}
