<?php
namespace FleetTrackPro\Controllers;

use FleetTrackPro\Repositories\DriverRepository;
use FleetTrackPro\Services\AuthService;
use WP_REST_Request;

class DriverController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new DriverRepository();
    }

    /**
     * GET /drivers
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'name', 'email', 'phone', 'license_expiry', 'status'];
        $search_fields = ['name', 'email', 'phone', 'license_number'];
        
        $extra_filters = [];
        if (!empty($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $result = $this->repository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Drivers fetched successfully', $result);
    }

    /**
     * GET /drivers/{id}
     */
    public function show(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $driver = $this->repository->findById($id);

        if (!$driver) {
            return $this->error('Driver not found.', [], 404);
        }

        return $this->success('Driver details fetched successfully', $driver);
    }

    /**
     * POST /drivers
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['name']) || empty($params['email']) || empty($params['phone']) || empty($params['license_number'])) {
            return $this->error('Validation failed: name, email, phone, license_number are required.');
        }

        $email = sanitize_email($params['email']);
        $phone = sanitize_text_field($params['phone']);
        $license = sanitize_text_field($params['license_number']);

        if ($this->repository->existsEmail($email)) {
            return $this->error("Email '$email' already registered.");
        }
        if ($this->repository->existsPhone($phone)) {
            return $this->error("Phone '$phone' already registered.");
        }
        if ($this->repository->existsLicense($license)) {
            return $this->error("License number '$license' already registered.");
        }

        $data = [
            'name' => sanitize_text_field($params['name']),
            'email' => $email,
            'phone' => $phone,
            'license_number' => $license,
            'license_expiry' => !empty($params['license_expiry']) ? sanitize_text_field($params['license_expiry']) : null,
            'salary' => !empty($params['salary']) ? (float)$params['salary'] : 0.00,
            'joining_date' => !empty($params['joining_date']) ? sanitize_text_field($params['joining_date']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s'];
        $driver_id = $this->repository->create($data, $formats);

        if (!$driver_id) {
            return $this->error('Failed to register driver.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_DRIVER', "Registered driver $license (ID: $driver_id)");

        return $this->success('Driver created successfully', array_merge(['id' => $driver_id], $data), 201);
    }

    /**
     * PUT /drivers/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $driver = $this->repository->findById($id);

        if (!$driver) {
            return $this->error('Driver not found.', [], 404);
        }

        $params = $request->get_json_params();
        $update_data = [];
        $formats = [];

        if (isset($params['email'])) {
            $email = sanitize_email($params['email']);
            if ($this->repository->existsEmail($email, $id)) {
                return $this->error("Email '$email' already exists.");
            }
            $update_data['email'] = $email;
            $formats[] = '%s';
        }

        if (isset($params['phone'])) {
            $phone = sanitize_text_field($params['phone']);
            if ($this->repository->existsPhone($phone, $id)) {
                return $this->error("Phone '$phone' already exists.");
            }
            $update_data['phone'] = $phone;
            $formats[] = '%s';
        }

        if (isset($params['license_number'])) {
            $license = sanitize_text_field($params['license_number']);
            if ($this->repository->existsLicense($license, $id)) {
                return $this->error("License number '$license' already exists.");
            }
            $update_data['license_number'] = $license;
            $formats[] = '%s';
        }

        $allowed_fields = [
            'name' => '%s',
            'license_expiry' => '%s',
            'salary' => '%f',
            'joining_date' => '%s',
            'status' => '%s'
        ];

        foreach ($allowed_fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%f') {
                    $update_data[$field] = (float)$params[$field];
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
            return $this->error('Failed to update driver details.');
        }

        AuthService::logActivity(get_current_user_id(), 'UPDATE_DRIVER', "Updated driver ID: $id");

        return $this->success('Driver updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /drivers/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $driver = $this->repository->findById($id);

        if (!$driver) {
            return $this->error('Driver not found.', [], 404);
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            return $this->error('Failed to delete driver.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELETE_DRIVER', "Soft deleted driver ID: $id");

        return $this->success('Driver soft deleted successfully');
    }
}
