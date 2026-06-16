<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\InventoryRepository;
use ManufacturingManagementApi\Repositories\RawMaterialRepository;
use ManufacturingManagementApi\Repositories\FinishedGoodsRepository;
use WP_REST_Request;

class InventoryController extends BaseController {
    private $repo;
    private $rawRepo;
    private $fgRepo;

    public function __construct() {
        $this->repo = new InventoryRepository();
        $this->rawRepo = new RawMaterialRepository();
        $this->fgRepo = new FinishedGoodsRepository();
    }

    public function getInventorySummary(WP_REST_Request $request) {
        $raw = $this->rawRepo->all();
        $fg = $this->fgRepo->all();
        
        return $this->success('Inventory summary retrieved successfully.', [
            'raw_materials' => $raw,
            'finished_goods' => $fg
        ]);
    }

    public function createAdjustment(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['item_type']) || empty($params['item_id']) || !isset($params['quantity']) || empty($params['movement_type'])) {
            return $this->error('Validation failed: item_type, item_id, quantity, and movement_type are required.');
        }

        $item_type = strtoupper($params['item_type']); // RAW or FINISHED
        $item_id = intval($params['item_id']);
        $quantity = floatval($params['quantity']);
        $movement_type = strtoupper($params['movement_type']); // IN, OUT, ADJUSTMENT
        $reference = sanitize_text_field($params['reference'] ?? 'Manual Adjustment');

        if ($item_type === 'RAW') {
            $mat = $this->rawRepo->find($item_id);
            if (!$mat) {
                return $this->error('Raw material not found.');
            }
            $current = floatval($mat['current_stock']);
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
            $this->rawRepo->update($item_id, ['current_stock' => $new_stock]);
        } else {
            $prod = $this->fgRepo->find($item_id);
            if (!$prod) {
                return $this->error('Finished product not found.');
            }
            $current = floatval($prod['quantity']);
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
            $this->fgRepo->update($item_id, ['quantity' => $new_stock]);
        }

        $this->repo->logMovement($item_type, $item_id, $movement_type, $quantity, $reference);
        return $this->success('Inventory stock adjusted successfully.');
    }

    public function getLowStock(WP_REST_Request $request) {
        $items = $this->repo->getLowStockRawMaterials();
        return $this->success('Low stock raw materials retrieved successfully.', $items);
    }

    public function getStockMovement(WP_REST_Request $request) {
        $items = $this->repo->all();
        foreach ($items as &$item) {
            if ($item['item_type'] === 'RAW') {
                $mat = $this->rawRepo->find(intval($item['item_id']));
                $item['item_name'] = $mat ? $mat['material_name'] : 'Unknown Raw Material';
            } else {
                $prod = $this->fgRepo->find(intval($item['item_id']));
                $item['item_name'] = $prod ? $prod['product_name'] : 'Unknown Finished Good';
            }
        }
        return $this->success('Stock movement logs retrieved successfully.', $items);
    }
}
