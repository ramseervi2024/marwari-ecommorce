<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\RouteRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class RouteController extends BaseController {
    private $routeRepository;

    public function __construct() {
        $this->routeRepository = new RouteRepository();
    }

    /**
     * GET /routes
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'route_code', 'source', 'destination', 'distance_km', 'toll_charges', 'status', 'created_at'];
        $search_fields = ['route_code', 'source', 'destination'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->routeRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Routes retrieved successfully.', $results);
    }

    /**
     * GET /routes/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $route = $this->routeRepository->findById($id);

        if (!$route) {
            return $this->error('Route not found.', [], 404);
        }

        return $this->success('Route retrieved successfully.', $route);
    }

    /**
     * POST /routes
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['source']) || empty($params['destination'])) {
            return $this->error('Validation failed: source and destination are required.');
        }

        // Generate custom route code
        $route_code = 'RTE-' . strtoupper(substr(sanitize_key($params['source']), 0, 3)) . '-' . strtoupper(substr(sanitize_key($params['destination']), 0, 3));
        if ($this->routeRepository->existsRouteCode($route_code)) {
            $route_code .= '-' . sprintf('%02d', rand(1, 99));
        }

        $data = [
            'route_code' => $route_code,
            'source' => sanitize_text_field($params['source']),
            'destination' => sanitize_text_field($params['destination']),
            'distance_km' => intval($params['distance_km'] ?? 0),
            'estimated_time' => sanitize_text_field($params['estimated_time'] ?? ''),
            'toll_charges' => floatval($params['toll_charges'] ?? 0.00),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%d', '%s', '%f', '%s'];
        $inserted_id = $this->routeRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create route.');
        }

        AuthService::logActivity(get_current_user_id(), 'ROUTE_CREATE', "Created route $route_code ($inserted_id)");

        return $this->success('Route created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /routes/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $route = $this->routeRepository->findById($id);

        if (!$route) {
            return $this->error('Route not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'source' => '%s',
            'destination' => '%s',
            'distance_km' => '%d',
            'estimated_time' => '%s',
            'toll_charges' => '%f',
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

        $updated = $this->routeRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update route.');
        }

        AuthService::logActivity(get_current_user_id(), 'ROUTE_UPDATE', "Updated route ID: $id");

        return $this->success('Route updated successfully.', $this->routeRepository->findById($id));
    }

    /**
     * DELETE /routes/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $route = $this->routeRepository->findById($id);

        if (!$route) {
            return $this->error('Route not found.', [], 404);
        }

        $deleted = $this->routeRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete route.');
        }

        AuthService::logActivity(get_current_user_id(), 'ROUTE_DELETE', "Soft deleted route ID: $id ($route[route_code])");

        return $this->success('Route deleted successfully.');
    }
}
