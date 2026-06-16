<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\QualityRepository;
use ManufacturingManagementApi\Repositories\WorkOrderRepository;
use ManufacturingManagementApi\Repositories\FinishedGoodsRepository;
use ManufacturingManagementApi\Repositories\InventoryRepository;
use WP_REST_Request;

class QualityController extends BaseController {
    private $repo;
    private $woRepo;
    private $fgRepo;
    private $invRepo;

    public function __construct() {
        $this->repo = new QualityRepository();
        $this->woRepo = new WorkOrderRepository();
        $this->fgRepo = new FinishedGoodsRepository();
        $this->invRepo = new InventoryRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        foreach ($items as &$item) {
            $product = $this->fgRepo->find(intval($item['product_id']));
            $item['product_name'] = $product ? $product['product_name'] : 'Unknown';
            $item['product_code'] = $product ? $product['product_code'] : 'Unknown';

            $wo = $this->woRepo->find(intval($item['work_order_id']));
            $item['work_order_number'] = $wo ? $wo['work_order_number'] : 'Unknown';
        }
        return $this->success('Quality checks retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['inspection_number']) || empty($params['work_order_id']) || empty($params['product_id']) || !isset($params['approved_quantity']) || !isset($params['rejected_quantity'])) {
            return $this->error('Validation failed: inspection_number, work_order_id, product_id, approved_quantity, and rejected_quantity are required.');
        }

        $params['work_order_id'] = intval($params['work_order_id']);
        $params['product_id'] = intval($params['product_id']);
        $params['approved_quantity'] = floatval($params['approved_quantity']);
        $params['rejected_quantity'] = floatval($params['rejected_quantity']);
        $params['inspection_date'] = !empty($params['inspection_date']) ? sanitize_text_field($params['inspection_date']) : current_time('mysql');
        $params['remarks'] = sanitize_textarea_field($params['remarks'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? 'PASSED');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create quality check. Ensure inspection_number is unique.');
        }

        // Increment finished goods stock by approved quantity
        $prod = $this->fgRepo->find($params['product_id']);
        if ($prod) {
            $new_fg_stock = floatval($prod['quantity']) + $params['approved_quantity'];
            $this->fgRepo->update($prod['id'], ['quantity' => $new_fg_stock]);
            $this->invRepo->logMovement('FINISHED', $prod['id'], 'IN', $params['approved_quantity'], 'QC Passed: ' . $params['inspection_number']);
        }

        return $this->success('Quality check recorded successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Quality check not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete quality check.');
        }
        return $this->success('Quality check deleted successfully.');
    }
}
