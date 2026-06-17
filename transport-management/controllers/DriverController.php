<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\DriverRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class DriverController extends BaseController {
    private $driverRepository;

    public function __construct() {
        $this->driverRepository = new DriverRepository();
    }

    /**
     * GET /drivers
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'driver_code', 'name', 'mobile', 'joining_date', 'status', 'created_at'];
        $search_fields = ['driver_code', 'name', 'mobile', 'license_number'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['salary_type'])) {
            $extra_filters['salary_type'] = sanitize_text_field($params['salary_type']);
        }

        $results = $this->driverRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Drivers retrieved successfully.', $results);
    }

    /**
     * GET /drivers/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $driver = $this->driverRepository->findById($id);

        if (!$driver) {
            return $this->error('Driver not found.', [], 404);
        }

        return $this->success('Driver retrieved successfully.', $driver);
    }

    /**
     * POST /drivers
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['name']) || empty($params['mobile']) || empty($params['license_number'])) {
            return $this->error('Validation failed: name, mobile, and license_number are required.');
        }

        // Generate custom driver code
        $driver_code = 'DRV-' . sprintf('%04d', rand(1000, 9999));
        while ($this->driverRepository->existsDriverCode($driver_code)) {
            $driver_code = 'DRV-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'driver_code' => $driver_code,
            'name' => sanitize_text_field($params['name']),
            'mobile' => sanitize_text_field($params['mobile']),
            'license_number' => sanitize_text_field($params['license_number']),
            'license_expiry' => !empty($params['license_expiry']) ? sanitize_text_field($params['license_expiry']) : null,
            'joining_date' => !empty($params['joining_date']) ? sanitize_text_field($params['joining_date']) : null,
            'salary_type' => sanitize_text_field($params['salary_type'] ?? 'fixed'),
            'fixed_salary' => floatval($params['fixed_salary'] ?? 0.00),
            'per_trip_salary' => floatval($params['per_trip_salary'] ?? 0.00),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s'];
        $inserted_id = $this->driverRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create driver.');
        }

        AuthService::logActivity(get_current_user_id(), 'DRIVER_CREATE', "Created driver $driver_code ($inserted_id)");

        return $this->success('Driver created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /drivers/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $driver = $this->driverRepository->findById($id);

        if (!$driver) {
            return $this->error('Driver not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'name' => '%s',
            'mobile' => '%s',
            'license_number' => '%s',
            'license_expiry' => '%s',
            'joining_date' => '%s',
            'salary_type' => '%s',
            'fixed_salary' => '%f',
            'per_trip_salary' => '%f',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%f') {
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

        $updated = $this->driverRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update driver.');
        }

        AuthService::logActivity(get_current_user_id(), 'DRIVER_UPDATE', "Updated driver ID: $id");

        return $this->success('Driver updated successfully.', $this->driverRepository->findById($id));
    }

    /**
     * DELETE /drivers/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $driver = $this->driverRepository->findById($id);

        if (!$driver) {
            return $this->error('Driver not found.', [], 404);
        }

        $deleted = $this->driverRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete driver.');
        }

        AuthService::logActivity(get_current_user_id(), 'DRIVER_DELETE', "Soft deleted driver ID: $id ($driver[driver_code])");

        return $this->success('Driver deleted successfully.');
    }
}
