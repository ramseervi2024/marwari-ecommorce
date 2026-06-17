<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\InventoryRepository;
use AccountingManagementApi\Repositories\ItemRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class InventoryController extends BaseController {
    private $inventoryRepository;
    private $itemRepository;

    public function __construct() {
        $this->inventoryRepository = new InventoryRepository();
        $this->itemRepository = new ItemRepository();
    }

    /**
     * GET /inventory
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'item_id', 'stock_quantity', 'minimum_stock'];
        $search_fields = ['warehouse'];
        
        $extra_filters = [];
        if (isset($params['item_id'])) {
            $extra_filters['item_id'] = intval($params['item_id']);
        }

        $results = $this->inventoryRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        foreach ($results['data'] as &$row) {
            $item = $this->itemRepository->findById($row['item_id']);
            $row['item_code'] = $item ? $item['item_code'] : 'Unknown';
            $row['item_name'] = $item ? $item['item_name'] : 'Unknown';
        }

        return $this->success('Inventory levels retrieved successfully.', $results);
    }

    /**
     * POST /inventory/adjust
     */
    public function adjust(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['item_id']) || !isset($params['stock_quantity'])) {
            return $this->error('item_id and stock_quantity are required.');
        }

        $item_id = intval($params['item_id']);
        $qty = intval($params['stock_quantity']);
        $min_stock = intval($params['minimum_stock'] ?? 10);
        $warehouse = sanitize_text_field($params['warehouse'] ?? 'Default Warehouse');

        $item = $this->itemRepository->findById($item_id);
        if (!$item) {
            return $this->error('Item not found.');
        }

        // Check if inventory record exists
        $existing = $this->inventoryRepository->findByItemId($item_id);
        if ($existing) {
            $updated = $this->inventoryRepository->update($existing['id'], [
                'stock_quantity' => $qty,
                'minimum_stock' => $min_stock,
                'warehouse' => $warehouse
            ], ['%d', '%d', '%s']);
            $id = $existing['id'];
        } else {
            $id = $this->inventoryRepository->create([
                'item_id' => $item_id,
                'stock_quantity' => $qty,
                'minimum_stock' => $min_stock,
                'warehouse' => $warehouse
            ], ['%d', '%d', '%d', '%s']);
            $updated = ($id !== null);
        }

        if (!$updated) {
            return $this->error('Failed to adjust inventory level.');
        }

        // Sync main item table stock quantity
        $this->itemRepository->update($item_id, ['stock_quantity' => $qty], ['%d']);

        AuthService::logActivity(get_current_user_id(), 'INVENTORY_ADJUST', "Adjusted stock for item ID: $item_id to $qty");

        return $this->success('Inventory updated successfully.', [
            'id' => $id,
            'item_id' => $item_id,
            'stock_quantity' => $qty,
            'minimum_stock' => $min_stock,
            'warehouse' => $warehouse
        ]);
    }
}
