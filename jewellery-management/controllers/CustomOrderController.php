<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\CustomOrderRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class CustomOrderController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new CustomOrderRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Custom orders retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Custom order not found.', [], 404);
        }
        return $this->success('Custom order retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['customer_id'])) {
            return $this->error('Validation failed: customer_id is required.');
        }

        $params['order_number'] = sanitize_text_field($params['order_number'] ?? 'ORD-' . rand(10000, 99999));
        $params['customer_id'] = intval($params['customer_id']);
        $params['design_reference'] = sanitize_text_field($params['design_reference'] ?? '');
        $params['metal_type'] = sanitize_text_field($params['metal_type'] ?? 'Gold');
        $params['purity'] = sanitize_text_field($params['purity'] ?? '');
        $params['weight_estimate'] = floatval($params['weight_estimate'] ?? 0);
        $params['advance_amount'] = floatval($params['advance_amount'] ?? 0);
        $params['delivery_date'] = sanitize_text_field($params['delivery_date'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? 'Pending');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create custom order.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOM_ORDER_CREATE', "Booked custom order {$params['order_number']} with advance of {$params['advance_amount']} INR");

        return $this->success('Custom order created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Custom order not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['design_reference'])) $updates['design_reference'] = sanitize_text_field($params['design_reference']);
        if (isset($params['metal_type'])) $updates['metal_type'] = sanitize_text_field($params['metal_type']);
        if (isset($params['purity'])) $updates['purity'] = sanitize_text_field($params['purity']);
        if (isset($params['weight_estimate'])) $updates['weight_estimate'] = floatval($params['weight_estimate']);
        if (isset($params['advance_amount'])) $updates['advance_amount'] = floatval($params['advance_amount']);
        if (isset($params['delivery_date'])) $updates['delivery_date'] = sanitize_text_field($params['delivery_date']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update custom order.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOM_ORDER_UPDATE', "Updated custom order ID $id");

        return $this->success('Custom order updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Custom order not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete custom order.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOM_ORDER_DELETE', "Deleted custom order: {$item['order_number']}");

        return $this->success('Custom order deleted successfully.');
    }
}
