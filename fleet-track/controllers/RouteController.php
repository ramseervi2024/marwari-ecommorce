<?php
namespace FleetTrackPro\Controllers;

use FleetTrackPro\Repositories\RouteRepository;
use FleetTrackPro\Services\AuthService;
use WP_REST_Request;

class RouteController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new RouteRepository();
    }

    /**
     * GET /routes
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'route_name', 'distance_km', 'status'];
        $search_fields = ['route_name', 'source', 'destination'];
        
        $extra_filters = [];
        if (!empty($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $result = $this->repository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Routes fetched successfully', $result);
    }

    /**
     * GET /routes/{id}
     */
    public function show(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $route = $this->repository->findById($id);

        if (!$route) {
            return $this->error('Route not found.', [], 404);
        }

        return $this->success('Route details fetched successfully', $route);
    }

    /**
     * POST /routes
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['route_name']) || empty($params['source']) || empty($params['destination']) || !isset($params['distance_km'])) {
            return $this->error('Validation failed: route_name, source, destination, distance_km are required.');
        }

        $data = [
            'route_name' => sanitize_text_field($params['route_name']),
            'source' => sanitize_text_field($params['source']),
            'destination' => sanitize_text_field($params['destination']),
            'distance_km' => (float)$params['distance_km'],
            'estimated_time' => sanitize_text_field($params['estimated_time'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s'];
        $route_id = $this->repository->create($data, $formats);

        if (!$route_id) {
            return $this->error('Failed to create route.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_ROUTE', "Created route {$data['route_name']} (ID: $route_id)");

        return $this->success('Route created successfully', array_merge(['id' => $route_id], $data), 201);
    }

    /**
     * PUT /routes/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $route = $this->repository->findById($id);

        if (!$route) {
            return $this->error('Route not found.', [], 404);
        }

        $params = $request->get_json_params();
        $update_data = [];
        $formats = [];

        $allowed_fields = [
            'route_name' => '%s',
            'source' => '%s',
            'destination' => '%s',
            'distance_km' => '%f',
            'estimated_time' => '%s',
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
            return $this->error('Failed to update route.');
        }

        AuthService::logActivity(get_current_user_id(), 'UPDATE_ROUTE', "Updated route ID: $id");

        return $this->success('Route updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /routes/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $route = $this->repository->findById($id);

        if (!$route) {
            return $this->error('Route not found.', [], 404);
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            return $this->error('Failed to delete route.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELETE_ROUTE', "Soft deleted route ID: $id");

        return $this->success('Route soft deleted successfully');
    }
}
