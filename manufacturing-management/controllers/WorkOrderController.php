<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\WorkOrderRepository;
use ManufacturingManagementApi\Repositories\BomRepository;
use ManufacturingManagementApi\Repositories\RawMaterialRepository;
use ManufacturingManagementApi\Repositories\InventoryRepository;
use ManufacturingManagementApi\Repositories\FinishedGoodsRepository;
use WP_REST_Request;

class WorkOrderController extends BaseController {
    private $repo;
    private $bomRepo;
    private $rawRepo;
    private $invRepo;
    private $prodRepo;

    public function __construct() {
        $this->repo = new WorkOrderRepository();
        $this->bomRepo = new BomRepository();
        $this->rawRepo = new RawMaterialRepository();
        $this->invRepo = new InventoryRepository();
        $this->prodRepo = new FinishedGoodsRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        foreach ($items as &$item) {
            $product = $this->prodRepo->find(intval($item['product_id']));
            $item['product_name'] = $product ? $product['product_name'] : 'Unknown';
            $item['product_code'] = $product ? $product['product_code'] : 'Unknown';
        }
        return $this->success('Work orders retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['work_order_number']) || empty($params['product_id']) || empty($params['quantity'])) {
            return $this->error('Validation failed: work_order_number, product_id, and quantity are required.');
        }

        $params['product_id'] = intval($params['product_id']);
        $params['production_plan_id'] = !empty($params['production_plan_id']) ? intval($params['production_plan_id']) : null;
        $params['quantity'] = floatval($params['quantity']);
        $params['assigned_to'] = !empty($params['assigned_to']) ? intval($params['assigned_to']) : null;
        $params['start_date'] = !empty($params['start_date']) ? sanitize_text_field($params['start_date']) : null;
        $params['end_date'] = !empty($params['end_date']) ? sanitize_text_field($params['end_date']) : null;
        $params['status'] = sanitize_text_field($params['status'] ?? 'PENDING');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        // Check if starting instantly
        if ($params['status'] === 'In Progress') {
            $check = $this->checkAndDeductStock($params['product_id'], $params['quantity'], $params['work_order_number']);
            if (is_wp_error($check)) {
                return $this->error($check->get_error_message());
            }
        }

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create work order. Ensure work_order_number is unique.');
        }

        return $this->success('Work order created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Work order not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        if (isset($params['assigned_to'])) $updates['assigned_to'] = intval($params['assigned_to']);
        if (isset($params['start_date'])) $updates['start_date'] = sanitize_text_field($params['start_date']);
        if (isset($params['end_date'])) $updates['end_date'] = sanitize_text_field($params['end_date']);
        $updates['updated_at'] = current_time('mysql');

        // Transitioning to 'In Progress' triggers stock verification and auto-deduction
        if (isset($params['status']) && $params['status'] === 'In Progress' && $item['status'] !== 'In Progress') {
            $check = $this->checkAndDeductStock(intval($item['product_id']), floatval($item['quantity']), $item['work_order_number']);
            if (is_wp_error($check)) {
                return $this->error($check->get_error_message());
            }
        }

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update work order.');
        }

        return $this->success('Work order updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Work order not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete work order.');
        }
        return $this->success('Work order deleted successfully.');
    }

    /**
     * Check and deduct raw material stock based on Bill of Materials
     */
    private function checkAndDeductStock(int $product_id, float $wo_qty, string $wo_number) {
        $bom_items = $this->bomRepo->getByProduct($product_id);
        if (empty($bom_items)) {
            return true; // No raw materials required for this product
        }

        // 1. Verify all stocks first
        $materials_to_deduct = [];
        foreach ($bom_items as $bom) {
            $mat = $this->rawRepo->find(intval($bom['material_id']));
            if (!$mat) {
                return new \WP_Error('material_not_found', "BOM Raw Material ID {$bom['material_id']} not found in catalog.");
            }

            $needed = floatval($bom['required_quantity']) * $wo_qty;
            if (floatval($mat['current_stock']) < $needed) {
                return new \WP_Error(
                    'insufficient_stock',
                    "Insufficient stock for raw material '{$mat['material_name']}'. Required: {$needed} {$mat['unit']}, Available: {$mat['current_stock']} {$mat['unit']}."
                );
            }
            $materials_to_deduct[] = [
                'material' => $mat,
                'deduct_qty' => $needed
            ];
        }

        // 2. Perform actual stock deduction
        foreach ($materials_to_deduct as $item) {
            $mat = $item['material'];
            $deduct_qty = $item['deduct_qty'];
            $new_stock = floatval($mat['current_stock']) - $deduct_qty;
            
            $this->rawRepo->update($mat['id'], ['current_stock' => $new_stock]);
            $this->invRepo->logMovement('RAW', $mat['id'], 'OUT', $deduct_qty, 'WO Issued: ' . $wo_number);
        }

        return true;
    }
}
