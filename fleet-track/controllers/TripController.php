<?php
namespace FleetTrackPro\Controllers;

use FleetTrackPro\Repositories\TripRepository;
use FleetTrackPro\Services\AuthService;
use WP_REST_Request;

class TripController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new TripRepository();
    }

    /**
     * GET /trips
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $result = $this->repository->findAllWithDetails($params);
        return $this->success('Trips fetched successfully', $result);
    }

    /**
     * GET /trips/{id}
     */
    public function show(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $trip = $this->repository->findTripWithDetails($id);

        if (!$trip) {
            return $this->error('Trip not found.', [], 404);
        }

        return $this->success('Trip details fetched successfully', $trip);
    }

    /**
     * POST /trips
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['vehicle_id']) || empty($params['driver_id']) || empty($params['route_id']) || empty($params['trip_date'])) {
            return $this->error('Validation failed: vehicle_id, driver_id, route_id, trip_date are required.');
        }

        $start_km = isset($params['start_km']) ? (float)$params['start_km'] : 0.00;
        $end_km = isset($params['end_km']) ? (float)$params['end_km'] : 0.00;
        $distance = $end_km > $start_km ? ($end_km - $start_km) : 0.00;
        $revenue = isset($params['revenue']) ? (float)$params['revenue'] : 0.00;

        $data = [
            'vehicle_id' => (int)$params['vehicle_id'],
            'driver_id' => (int)$params['driver_id'],
            'route_id' => (int)$params['route_id'],
            'trip_date' => sanitize_text_field($params['trip_date']),
            'start_km' => $start_km,
            'end_km' => $end_km,
            'distance_travelled' => $distance,
            'revenue' => $revenue,
            'status' => sanitize_text_field($params['status'] ?? 'PLANNED'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%d', '%d', '%d', '%s', '%f', '%f', '%f', '%f', '%s', '%s', '%s'];
        $trip_id = $this->repository->create($data, $formats);

        if (!$trip_id) {
            return $this->error('Failed to register trip.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_TRIP', "Registered new trip (ID: $trip_id) for Vehicle ID: {$data['vehicle_id']}");

        return $this->success('Trip created successfully', $this->repository->findTripWithDetails($trip_id), 201);
    }

    /**
     * PUT /trips/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $trip = $this->repository->findById($id);

        if (!$trip) {
            return $this->error('Trip not found.', [], 404);
        }

        $params = $request->get_json_params();
        $update_data = [];
        $formats = [];

        $allowed_fields = [
            'vehicle_id' => '%d',
            'driver_id' => '%d',
            'route_id' => '%d',
            'trip_date' => '%s',
            'start_km' => '%f',
            'end_km' => '%f',
            'revenue' => '%f',
            'status' => '%s'
        ];

        foreach ($allowed_fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $update_data[$field] = (int)$params[$field];
                } elseif ($format === '%f') {
                    $update_data[$field] = (float)$params[$field];
                } else {
                    $update_data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        // Recalculate distance_travelled if start_km or end_km is updated
        $start_km = isset($update_data['start_km']) ? $update_data['start_km'] : (float)$trip['start_km'];
        $end_km = isset($update_data['end_km']) ? $update_data['end_km'] : (float)$trip['end_km'];
        if (isset($update_data['start_km']) || isset($update_data['end_km'])) {
            $update_data['distance_travelled'] = $end_km > $start_km ? ($end_km - $start_km) : 0.00;
            $formats[] = '%f';
        }

        if (empty($update_data)) {
            return $this->error('No parameters provided for update.');
        }

        $update_data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->repository->update($id, $update_data, $formats);

        if (!$success) {
            return $this->error('Failed to update trip.');
        }

        AuthService::logActivity(get_current_user_id(), 'UPDATE_TRIP', "Updated trip ID: $id");

        return $this->success('Trip updated successfully', $this->repository->findTripWithDetails($id));
    }

    /**
     * DELETE /trips/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $trip = $this->repository->findById($id);

        if (!$trip) {
            return $this->error('Trip not found.', [], 404);
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            return $this->error('Failed to delete trip.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELETE_TRIP', "Soft deleted trip ID: $id");

        return $this->success('Trip soft deleted successfully');
    }
}
