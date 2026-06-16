<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\PurchaseRepository;
use ManufacturingManagementApi\Repositories\RawMaterialRepository;
use ManufacturingManagementApi\Repositories\InventoryRepository;
use WP_REST_Request;

class PurchaseController extends BaseController {
    private $repo;
    private $rawRepo;
    private $invRepo;

    public function __construct() {
        $this->repo = new PurchaseRepository();
        $this->rawRepo = new RawMaterialRepository();
        $this->invRepo = new InventoryRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        return $this->success('Purchases retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['po_number']) || empty($params['supplier_id']) || empty($params['material_id']) || empty($params['quantity'])) {
            return $this->error('Validation failed: po_number, supplier_id, material_id, and quantity are required.');
        }

        $material = $this->rawRepo->find(intval($params['material_id']));
        if (!$material) {
            return $this->error('Raw material not found.');
        }

        $params['quantity'] = floatval($params['quantity']);
        $params['rate'] = floatval($params['rate'] ?? $material['purchase_price']);
        $params['gst_amount'] = floatval($params['gst_amount'] ?? 0);
        $params['total_amount'] = ($params['quantity'] * $params['rate']) + $params['gst_amount'];
        $params['purchase_date'] = !empty($params['purchase_date']) ? sanitize_text_field($params['purchase_date']) : current_time('mysql');
        $params['status'] = sanitize_text_field($params['status'] ?? 'PENDING');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create purchase order. Ensure po_number is unique.');
        }

        // If instantly completed
        if ($params['status'] === 'COMPLETED') {
            $new_stock = floatval($material['current_stock']) + $params['quantity'];
            $this->rawRepo->update($material['id'], ['current_stock' => $new_stock]);
            $this->invRepo->logMovement('RAW', $material['id'], 'IN', $params['quantity'], 'PO: ' . $params['po_number']);
        }

        return $this->success('Purchase order created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Purchase order not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        if (isset($params['quantity'])) $updates['quantity'] = floatval($params['quantity']);
        if (isset($params['rate'])) $updates['rate'] = floatval($params['rate']);
        if (isset($params['gst_amount'])) $updates['gst_amount'] = floatval($params['gst_amount']);
        
        if (isset($updates['quantity']) || isset($updates['rate']) || isset($updates['gst_amount'])) {
            $qty = $updates['quantity'] ?? floatval($item['quantity']);
            $rate = $updates['rate'] ?? floatval($item['rate']);
            $gst = $updates['gst_amount'] ?? floatval($item['gst_amount']);
            $updates['total_amount'] = ($qty * $rate) + $gst;
        }
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update purchase order.');
        }

        // Transitioning to COMPLETED triggers inventory increment
        if (isset($params['status']) && $params['status'] === 'COMPLETED' && $item['status'] !== 'COMPLETED') {
            $material = $this->rawRepo->find(intval($item['material_id']));
            if ($material) {
                $qty = isset($updates['quantity']) ? $updates['quantity'] : floatval($item['quantity']);
                $new_stock = floatval($material['current_stock']) + $qty;
                $this->rawRepo->update($material['id'], ['current_stock' => $new_stock]);
                $this->invRepo->logMovement('RAW', $material['id'], 'IN', $qty, 'PO Rec: ' . $item['po_number']);
            }
        }

        return $this->success('Purchase order updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Purchase order not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete purchase order.');
        }
        return $this->success('Purchase order deleted successfully.');
    }
}
