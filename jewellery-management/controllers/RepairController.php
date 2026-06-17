<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\RepairRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class RepairController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new RepairRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Repair orders retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Repair order not found.', [], 404);
        }
        return $this->success('Repair order retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['customer_id']) || empty($params['product_description'])) {
            return $this->error('Validation failed: customer_id and product_description are required.');
        }

        $params['repair_number'] = sanitize_text_field($params['repair_number'] ?? 'REP-' . rand(10000, 99999));
        $params['customer_id'] = intval($params['customer_id']);
        $params['product_description'] = sanitize_text_field($params['product_description']);
        $params['issue_description'] = sanitize_textarea_field($params['issue_description'] ?? '');
        $params['received_weight'] = floatval($params['received_weight'] ?? 0);
        $params['repair_cost'] = floatval($params['repair_cost'] ?? 0);
        $params['expected_delivery'] = sanitize_text_field($params['expected_delivery'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? 'Received');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create repair order.');
        }

        AuthService::logActivity(get_current_user_id(), 'REPAIR_CREATE', "Registered repair order {$params['repair_number']} for item: {$params['product_description']}");

        return $this->success('Repair order created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Repair order not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['product_description'])) $updates['product_description'] = sanitize_text_field($params['product_description']);
        if (isset($params['issue_description'])) $updates['issue_description'] = sanitize_textarea_field($params['issue_description']);
        if (isset($params['received_weight'])) $updates['received_weight'] = floatval($params['received_weight']);
        if (isset($params['repair_cost'])) $updates['repair_cost'] = floatval($params['repair_cost']);
        if (isset($params['expected_delivery'])) $updates['expected_delivery'] = sanitize_text_field($params['expected_delivery']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update repair order.');
        }

        AuthService::logActivity(get_current_user_id(), 'REPAIR_UPDATE', "Updated repair order ID $id");

        return $this->success('Repair order updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Repair order not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete repair order.');
        }

        AuthService::logActivity(get_current_user_id(), 'REPAIR_DELETE', "Deleted repair order: {$item['repair_number']}");

        return $this->success('Repair order deleted successfully.');
    }
}
