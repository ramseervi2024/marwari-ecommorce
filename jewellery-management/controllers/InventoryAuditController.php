<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\InventoryAuditRepository;
use JewelleryManagementApi\Repositories\InventoryRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class InventoryAuditController extends BaseController {
    private $repo;
    private $inventoryRepo;

    public function __construct() {
        $this->repo = new InventoryAuditRepository();
        $this->inventoryRepo = new InventoryRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Inventory audits retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Inventory audit record not found.', [], 404);
        }
        return $this->success('Inventory audit record retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['item_id']) || !isset($params['physical_qty'])) {
            return $this->error('Validation failed: item_id and physical_qty are required.');
        }

        $item_id = intval($params['item_id']);
        $product = $this->inventoryRepo->find($item_id);
        if (!$product) {
            return $this->error('Inventory item not found.');
        }

        // system qty corresponds to gross_weight or count. Let's base it on gross_weight
        $system_qty = floatval($product['gross_weight']);
        $physical_qty = floatval($params['physical_qty']);
        $variance = $physical_qty - $system_qty;

        $params['audit_number'] = sanitize_text_field($params['audit_number'] ?? 'AUD-' . date('Ymd') . '-' . rand(100, 999));
        $params['auditor_name'] = sanitize_text_field($params['auditor_name'] ?? 'System Auditor');
        $params['item_id'] = $item_id;
        $params['physical_qty'] = $physical_qty;
        $params['system_qty'] = $system_qty;
        $params['variance'] = $variance;
        $params['status'] = sanitize_text_field($params['status'] ?? 'PENDING');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create inventory audit record.');
        }

        AuthService::logActivity(get_current_user_id(), 'AUDIT_CREATE', "Initiated inventory audit {$params['audit_number']} for Item ID $item_id. Variance: {$variance}g");

        return $this->success('Inventory audit record created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Inventory audit record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['status'])) {
            $updates['status'] = sanitize_text_field($params['status']);
            // If adjusted/approved, update actual item weight in inventory
            if ($updates['status'] === 'ADJUSTED' && $item['status'] !== 'ADJUSTED') {
                $this->inventoryRepo->update($item['item_id'], [
                    'gross_weight' => floatval($item['physical_qty']),
                    'net_weight' => floatval($item['physical_qty']),
                    'updated_at' => current_time('mysql')
                ]);
            }
        }
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update inventory audit record.');
        }

        AuthService::logActivity(get_current_user_id(), 'AUDIT_UPDATE', "Updated audit ID $id to status " . ($updates['status'] ?? $item['status']));

        return $this->success('Inventory audit record updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Inventory audit record not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete inventory audit record.');
        }

        return $this->success('Inventory audit record deleted successfully.');
    }
}
