<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\TransportRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class TransportController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new TransportRepository();
    }

    /**
     * GET /transport
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'route_name', 'vehicle_number', 'status'];
        $search_fields = ['route_name', 'source', 'destination', 'vehicle_number', 'driver_name'];
        $result = $this->repository->findAll($params, $allowed_sorts, $search_fields);
        return $this->success('Transport routes fetched successfully', $result);
    }

    /**
     * POST /transport
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['route_name']) || empty($params['source']) || empty($params['destination']) || empty($params['vehicle_number']) || empty($params['driver_name'])) {
            return $this->error('Validation failed: route_name, source, destination, vehicle_number, and driver_name are required.');
        }

        $data = [
            'route_name' => sanitize_text_field($params['route_name']),
            'source' => sanitize_text_field($params['source']),
            'destination' => sanitize_text_field($params['destination']),
            'vehicle_number' => sanitize_text_field($params['vehicle_number']),
            'driver_name' => sanitize_text_field($params['driver_name']),
            'driver_mobile' => isset($params['driver_mobile']) ? sanitize_text_field($params['driver_mobile']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $id = $this->repository->create($data, $formats);

        if (!$id) {
            return $this->error('Failed to create transport route.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_TRANSPORT', "Created transport route ID: $id");
        return $this->success('Transport route created successfully', array_merge(['id' => $id], $data), 201);
    }

    /**
     * PUT /transport/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $route = $this->repository->findById($id);

        if (!$route) {
            return $this->error('Transport route not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'route_name' => '%s',
            'source' => '%s',
            'destination' => '%s',
            'vehicle_number' => '%s',
            'driver_name' => '%s',
            'driver_mobile' => '%s',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                $data[$field] = sanitize_text_field($params[$field]);
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->repository->update($id, $data, $formats);
        return $this->success('Transport route updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /transport/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->repository->findById($id)) {
            return $this->error('Transport route not found.', [], 404);
        }

        $this->repository->delete($id);
        return $this->success('Transport route deleted successfully');
    }
}
