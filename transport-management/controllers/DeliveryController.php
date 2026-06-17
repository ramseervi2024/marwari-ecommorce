<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\DeliveryRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class DeliveryController extends BaseController {
    private $deliveryRepository;

    public function __construct() {
        $this->deliveryRepository = new DeliveryRepository();
    }

    /**
     * GET /deliveries
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'trip_id', 'tracking_number', 'delivery_status', 'created_at'];
        $search_fields = ['tracking_number', 'customer_name', 'delivery_address'];
        
        $extra_filters = [];
        if (isset($params['delivery_status'])) {
            $extra_filters['delivery_status'] = sanitize_text_field($params['delivery_status']);
        }
        if (isset($params['trip_id'])) {
            $extra_filters['trip_id'] = intval($params['trip_id']);
        }

        $results = $this->deliveryRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Deliveries retrieved successfully.', $results);
    }

    /**
     * GET /deliveries/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $delivery = $this->deliveryRepository->findById($id);

        if (!$delivery) {
            return $this->error('Delivery not found.', [], 404);
        }

        return $this->success('Delivery retrieved successfully.', $delivery);
    }

    /**
     * POST /deliveries
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['trip_id']) || empty($params['delivery_address'])) {
            return $this->error('Validation failed: trip_id and delivery_address are required.');
        }

        $tracking_number = 'TRK-' . sprintf('%07d', rand(1000000, 9999999));
        while ($this->deliveryRepository->existsTrackingNumber($tracking_number)) {
            $tracking_number = 'TRK-' . sprintf('%07d', rand(1000000, 9999999));
        }

        $data = [
            'trip_id' => intval($params['trip_id']),
            'tracking_number' => $tracking_number,
            'customer_name' => sanitize_text_field($params['customer_name'] ?? ''),
            'delivery_address' => sanitize_text_field($params['delivery_address']),
            'delivery_status' => sanitize_text_field($params['delivery_status'] ?? 'Picked Up'),
            'latitude' => sanitize_text_field($params['latitude'] ?? ''),
            'longitude' => sanitize_text_field($params['longitude'] ?? ''),
            'proof_of_delivery' => sanitize_text_field($params['proof_of_delivery'] ?? '')
        ];

        $formats = ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->deliveryRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create delivery tracking.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELIVERY_CREATE', "Created delivery tracking code $tracking_number ($inserted_id)");

        return $this->success('Delivery tracking created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /deliveries/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $delivery = $this->deliveryRepository->findById($id);

        if (!$delivery) {
            return $this->error('Delivery not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'trip_id' => '%d',
            'customer_name' => '%s',
            'delivery_address' => '%s',
            'delivery_status' => '%s',
            'latitude' => '%s',
            'longitude' => '%s',
            'proof_of_delivery' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $data[$field] = intval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->deliveryRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update delivery.');
        }

        // If status is updated to Delivered, check if we should update matching trip status to Delivered
        if (isset($data['delivery_status']) && $data['delivery_status'] === 'Delivered') {
            global $wpdb;
            $table_trips = $wpdb->prefix . 'transport_trips';
            $wpdb->update($table_trips, ['status' => 'Delivered'], ['id' => $delivery['trip_id']]);
        }

        AuthService::logActivity(get_current_user_id(), 'DELIVERY_UPDATE', "Updated delivery tracking ID: $id status: " . ($data['delivery_status'] ?? 'N/A'));

        return $this->success('Delivery updated successfully.', $this->deliveryRepository->findById($id));
    }

    /**
     * DELETE /deliveries/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $delivery = $this->deliveryRepository->findById($id);

        if (!$delivery) {
            return $this->error('Delivery not found.', [], 404);
        }

        $deleted = $this->deliveryRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete delivery.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELIVERY_DELETE', "Soft deleted delivery ID: $id ($delivery[tracking_number])");

        return $this->success('Delivery deleted successfully.');
    }
}
