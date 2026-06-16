<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\ProductionPlanRepository;
use ManufacturingManagementApi\Repositories\FinishedGoodsRepository;
use WP_REST_Request;

class ProductionPlanController extends BaseController {
    private $repo;
    private $prodRepo;

    public function __construct() {
        $this->repo = new ProductionPlanRepository();
        $this->prodRepo = new FinishedGoodsRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        foreach ($items as &$item) {
            $product = $this->prodRepo->find(intval($item['product_id']));
            $item['product_name'] = $product ? $product['product_name'] : 'Unknown';
            $item['product_code'] = $product ? $product['product_code'] : 'Unknown';
        }
        return $this->success('Production plans retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['plan_number']) || empty($params['product_id']) || empty($params['planned_quantity'])) {
            return $this->error('Validation failed: plan_number, product_id, and planned_quantity are required.');
        }

        $params['product_id'] = intval($params['product_id']);
        $params['planned_quantity'] = floatval($params['planned_quantity']);
        $params['planned_start_date'] = !empty($params['planned_start_date']) ? sanitize_text_field($params['planned_start_date']) : current_time('mysql');
        $params['planned_end_date'] = !empty($params['planned_end_date']) ? sanitize_text_field($params['planned_end_date']) : current_time('mysql');
        $params['priority'] = sanitize_text_field($params['priority'] ?? 'MEDIUM');
        $params['status'] = sanitize_text_field($params['status'] ?? 'PENDING');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create production plan. Ensure plan_number is unique.');
        }

        return $this->success('Production plan created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Production plan not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['planned_quantity'])) $updates['planned_quantity'] = floatval($params['planned_quantity']);
        if (isset($params['planned_start_date'])) $updates['planned_start_date'] = sanitize_text_field($params['planned_start_date']);
        if (isset($params['planned_end_date'])) $updates['planned_end_date'] = sanitize_text_field($params['planned_end_date']);
        if (isset($params['priority'])) $updates['priority'] = sanitize_text_field($params['priority']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update production plan.');
        }

        return $this->success('Production plan updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Production plan not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete production plan.');
        }
        return $this->success('Production plan deleted successfully.');
    }
}
