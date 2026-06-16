<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\DeliveryRepository;
use WP_REST_Request;

class DeliveryController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new DeliveryRepository();
    }

    public function getDeliveries(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $deliveries = $this->repository->all($limit, $offset);
        return $this->success('Deliveries retrieved successfully.', $deliveries);
    }

    public function createDelivery(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['order_id']) || empty($params['customer_address'])) {
            return $this->error('Validation failed: order_id and customer_address are required.');
        }

        $existing = $this->repository->findByOrderId(intval($params['order_id']));
        if ($existing) {
            return $this->success('Delivery already assigned for this order.', $existing);
        }

        $data = [
            'order_id' => intval($params['order_id']),
            'customer_address' => sanitize_textarea_field($params['customer_address']),
            'delivery_partner' => sanitize_text_field($params['delivery_partner'] ?? 'Internal Staff'),
            'delivery_charge' => floatval($params['delivery_charge'] ?? 0.00),
            'delivery_status' => sanitize_text_field($params['delivery_status'] ?? 'Assigned')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to register delivery.');
        }

        $data['id'] = $id;
        return $this->success('Delivery assigned successfully.', $data, 201);
    }

    public function updateDelivery(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $delivery = $this->repository->find($id);
        if (!$delivery) {
            return $this->error('Delivery not found.', [], 404);
        }

        $data = [];
        if (isset($params['delivery_partner'])) $data['delivery_partner'] = sanitize_text_field($params['delivery_partner']);
        if (isset($params['delivery_charge'])) $data['delivery_charge'] = floatval($params['delivery_charge']);
        if (isset($params['delivery_status'])) $data['delivery_status'] = sanitize_text_field($params['delivery_status']);
        if (isset($params['customer_address'])) $data['customer_address'] = sanitize_textarea_field($params['customer_address']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update delivery details.');
        }

        return $this->success('Delivery updated successfully.', array_merge($delivery, $data));
    }

    public function deleteDelivery(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $delivery = $this->repository->find($id);
        if (!$delivery) {
            return $this->error('Delivery not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete delivery.');
        }

        return $this->success('Delivery record deleted successfully.');
    }
}
