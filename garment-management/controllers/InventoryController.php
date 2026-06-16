<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\InventoryRepository;
use GarmentManagementApi\Repositories\FabricRepository;
use GarmentManagementApi\Repositories\AccessoryRepository;
use WP_REST_Request;

class InventoryController extends BaseController {
    private $repo;
    private $fabricRepo;
    private $accessoryRepo;

    public function __construct() {
        $this->repo = new InventoryRepository();
        $this->fabricRepo = new FabricRepository();
        $this->accessoryRepo = new AccessoryRepository();
    }

    public function getInventorySummary(WP_REST_Request $request) {
        $fabrics = $this->fabricRepo->all();
        $accessories = $this->accessoryRepo->all();
        
        return $this->success('Inventory summary retrieved successfully.', [
            'fabrics' => $fabrics,
            'accessories' => $accessories
        ]);
    }

    public function createAdjustment(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['item_type']) || empty($params['item_id']) || !isset($params['quantity']) || empty($params['movement_type'])) {
            return $this->error('Validation failed: item_type, item_id, quantity, and movement_type are required.');
        }

        $item_type = strtoupper($params['item_type']); // FABRIC, ACCESSORY, WIP, FINISHED
        $item_id = intval($params['item_id']);
        $quantity = floatval($params['quantity']);
        $movement_type = strtoupper($params['movement_type']); // IN, OUT, ADJUSTMENT
        $reference = sanitize_text_field($params['reference'] ?? 'Manual Adjustment');

        if ($item_type === 'FABRIC') {
            $fab = $this->fabricRepo->find($item_id);
            if (!$fab) {
                return $this->error('Fabric item not found.');
            }
            $current = floatval($fab['available_meters']);
            if ($movement_type === 'IN') {
                $new_stock = $current + $quantity;
            } elseif ($movement_type === 'OUT') {
                $new_stock = $current - $quantity;
            } else {
                $new_stock = $quantity; // Direct override
            }
            if ($new_stock < 0) {
                return $this->error('Negative stock is not permitted.');
            }
            $this->fabricRepo->update($item_id, ['available_meters' => $new_stock]);
        } elseif ($item_type === 'ACCESSORY') {
            $acc = $this->accessoryRepo->find($item_id);
            if (!$acc) {
                return $this->error('Accessory item not found.');
            }
            $current = floatval($acc['available_quantity']);
            if ($movement_type === 'IN') {
                $new_stock = $current + $quantity;
            } elseif ($movement_type === 'OUT') {
                $new_stock = $current - $quantity;
            } else {
                $new_stock = $quantity; // Direct override
            }
            if ($new_stock < 0) {
                return $this->error('Negative stock is not permitted.');
            }
            $this->accessoryRepo->update($item_id, ['available_quantity' => $new_stock]);
        } else {
            // WIP or FINISHED - direct transaction log
            // (since finished goods are linked to orders or style codes, we log it without an independent table)
        }

        $this->repo->logMovement($item_type, $item_id, $movement_type, $quantity, $reference);
        return $this->success('Inventory stock adjusted successfully.');
    }

    public function getLowStock(WP_REST_Request $request) {
        $items = $this->repo->getLowStockFabrics();
        return $this->success('Low stock fabrics retrieved successfully.', $items);
    }

    public function getStockMovement(WP_REST_Request $request) {
        $items = $this->repo->all();
        foreach ($items as &$item) {
            if ($item['item_type'] === 'FABRIC') {
                $fab = $this->fabricRepo->find(intval($item['item_id']));
                $item['item_name'] = $fab ? $fab['fabric_name'] . ' (' . $fab['color'] . ')' : 'Unknown Fabric';
            } elseif ($item['item_type'] === 'ACCESSORY') {
                $acc = $this->accessoryRepo->find(intval($item['item_id']));
                $item['item_name'] = $acc ? $acc['accessory_name'] : 'Unknown Accessory';
            } else {
                $item['item_name'] = $item['item_type'] . ' Item #' . $item['item_id'];
            }
        }
        return $this->success('Stock movement logs retrieved successfully.', $items);
    }
}
