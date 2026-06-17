<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\TripRepository;
use TransportManagementApi\Repositories\DeliveryRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class TripController extends BaseController {
    private $tripRepository;
    private $deliveryRepository;

    public function __construct() {
        $this->tripRepository = new TripRepository();
        $this->deliveryRepository = new DeliveryRepository();
    }

    /**
     * GET /trips
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'trip_number', 'vehicle_id', 'driver_id', 'route_id', 'freight_amount', 'status', 'created_at'];
        $search_fields = ['trip_number', 'customer_name', 'loading_point', 'unloading_point'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['driver_id'])) {
            $extra_filters['driver_id'] = intval($params['driver_id']);
        }
        if (isset($params['vehicle_id'])) {
            $extra_filters['vehicle_id'] = intval($params['vehicle_id']);
        }

        $results = $this->tripRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Trips retrieved successfully.', $results);
    }

    /**
     * GET /trips/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $trip = $this->tripRepository->findById($id);

        if (!$trip) {
            return $this->error('Trip not found.', [], 404);
        }

        return $this->success('Trip retrieved successfully.', $trip);
    }

    /**
     * POST /trips
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['vehicle_id']) || empty($params['driver_id']) || empty($params['route_id'])) {
            return $this->error('Validation failed: vehicle_id, driver_id, and route_id are required.');
        }

        // Generate custom trip number
        $trip_number = 'TRIP-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        while ($this->tripRepository->existsTripNumber($trip_number)) {
            $trip_number = 'TRIP-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'trip_number' => $trip_number,
            'vehicle_id' => intval($params['vehicle_id']),
            'driver_id' => intval($params['driver_id']),
            'route_id' => intval($params['route_id']),
            'customer_name' => sanitize_text_field($params['customer_name'] ?? ''),
            'loading_point' => sanitize_text_field($params['loading_point'] ?? ''),
            'unloading_point' => sanitize_text_field($params['unloading_point'] ?? ''),
            'trip_start_date' => !empty($params['trip_start_date']) ? sanitize_text_field($params['trip_start_date']) : null,
            'trip_end_date' => !empty($params['trip_end_date']) ? sanitize_text_field($params['trip_end_date']) : null,
            'freight_amount' => floatval($params['freight_amount'] ?? 0.00),
            'status' => sanitize_text_field($params['status'] ?? 'Assigned')
        ];

        $formats = ['%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->tripRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create trip.');
        }

        // Auto spawn delivery tracking entry
        $tracking_number = 'TRK-' . sprintf('%07d', rand(1000000, 9999999));
        while ($this->deliveryRepository->existsTrackingNumber($tracking_number)) {
            $tracking_number = 'TRK-' . sprintf('%07d', rand(1000000, 9999999));
        }

        $delivery_data = [
            'trip_id' => $inserted_id,
            'tracking_number' => $tracking_number,
            'customer_name' => $data['customer_name'],
            'delivery_address' => sanitize_text_field($params['delivery_address'] ?? $data['unloading_point']),
            'delivery_status' => 'Picked Up',
            'latitude' => '19.0760', // Default Mumbai
            'longitude' => '72.8777',
            'proof_of_delivery' => ''
        ];
        $this->deliveryRepository->create($delivery_data, ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);

        AuthService::logActivity(get_current_user_id(), 'TRIP_CREATE', "Created trip $trip_number ($inserted_id) and spawned delivery tracking $tracking_number");

        return $this->success('Trip created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /trips/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $trip = $this->tripRepository->findById($id);

        if (!$trip) {
            return $this->error('Trip not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'vehicle_id' => '%d',
            'driver_id' => '%d',
            'route_id' => '%d',
            'customer_name' => '%s',
            'loading_point' => '%s',
            'unloading_point' => '%s',
            'trip_start_date' => '%s',
            'trip_end_date' => '%s',
            'freight_amount' => '%f',
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

        $updated = $this->tripRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update trip.');
        }

        // If status changed to Delivered, update corresponding delivery status as well
        if (isset($data['status']) && $data['status'] === 'Delivered') {
            global $wpdb;
            $table_deliveries = $wpdb->prefix . 'transport_deliveries';
            $wpdb->update($table_deliveries, ['delivery_status' => 'Delivered'], ['trip_id' => $id]);
        }

        AuthService::logActivity(get_current_user_id(), 'TRIP_UPDATE', "Updated trip ID: $id");

        return $this->success('Trip updated successfully.', $this->tripRepository->findById($id));
    }

    /**
     * DELETE /trips/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $trip = $this->tripRepository->findById($id);

        if (!$trip) {
            return $this->error('Trip not found.', [], 404);
        }

        $deleted = $this->tripRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete trip.');
        }

        AuthService::logActivity(get_current_user_id(), 'TRIP_DELETE', "Soft deleted trip ID: $id ($trip[trip_number])");

        return $this->success('Trip deleted successfully.');
    }
}
