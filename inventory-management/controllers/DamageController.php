<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\DamageRepository;
use InventoryManagementApi\Repositories\StockRepository;
use InventoryManagementApi\Repositories\ProductRepository;
use InventoryManagementApi\Repositories\WarehouseRepository;
use InventoryManagementApi\Services\AuthService;
use WP_REST_Request;

class DamageController extends BaseController {
    private $damageRepository;
    private $stockRepository;
    private $productRepository;
    private $warehouseRepository;

    public function __construct() {
        $this->damageRepository = new DamageRepository();
        $this->stockRepository = new StockRepository();
        $this->productRepository = new ProductRepository();
        $this->warehouseRepository = new WarehouseRepository();
    }

    /**
     * GET /damaged-stock
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'product_id', 'warehouse_id', 'quantity', 'report_date', 'status'];
        $search_fields = ['status', 'remarks'];

        $results = $this->damageRepository->findAll($params, $allowed_sorts, $search_fields, []);
        
        foreach ($results['data'] as &$row) {
            $p = $this->productRepository->findById($row['product_id']);
            $w = $this->warehouseRepository->findById($row['warehouse_id']);
            $row['product_name'] = $p ? $p['product_name'] : 'Unknown';
            $row['sku'] = $p ? $p['sku'] : '';
            $row['warehouse_name'] = $w ? $w['warehouse_name'] : 'Unknown';
        }

        return $this->success('Damaged stock logs retrieved.', $results);
    }

    /**
     * POST /damaged-stock (Report stock damage)
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['product_id']) || empty($params['warehouse_id']) || empty($params['quantity'])) {
            return $this->error('product_id, warehouse_id, and quantity are required.');
        }

        $pid = intval($params['product_id']);
        $wid = intval($params['warehouse_id']);
        $qty = intval($params['quantity']);
        $report_date = sanitize_text_field($params['report_date'] ?? date('Y-m-d'));
        $remarks = sanitize_textarea_field($params['remarks'] ?? '');

        // Verify available stock
        $stock = $this->stockRepository->getStockRecord($pid, $wid);
        $avail = $stock ? (int)$stock['available_stock'] : 0;
        if ($avail < $qty) {
            return $this->error("Insufficient available stock to report as damaged. Available: $avail");
        }

        $data = [
            'product_id' => $pid,
            'warehouse_id' => $wid,
            'quantity' => $qty,
            'status' => 'Reported',
            'report_date' => $report_date,
            'remarks' => $remarks
        ];

        $formats = ['%d', '%d', '%d', '%s', '%s', '%s'];
        $inserted_id = $this->damageRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to log damaged stock.');
        }

        // Adjust stock: move available to damaged
        $this->stockRepository->adjustStock($pid, $wid, -$qty, 0, $qty);

        AuthService::logActivity(
            get_current_user_id(),
            'DAMAGE_REPORT',
            "Reported $qty units of product ID $pid as damaged in Warehouse ID $wid"
        );

        return $this->success('Damaged stock logged and warehouse inventories adjusted successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /damaged-stock/{id} (Update damage status e.g. Scrapped)
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $damage = $this->damageRepository->findById($id);

        if (!$damage) {
            return $this->error('Damage record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $status = sanitize_text_field($params['status'] ?? '');

        if (!in_array($status, ['Reported', 'Scrapped', 'Repaired'])) {
            return $this->error('Status must be Reported, Scrapped, or Repaired.');
        }

        if ($damage['status'] === $status) {
            return $this->success('Status already matches.', $damage);
        }

        $pid = $damage['product_id'];
        $wid = $damage['warehouse_id'];
        $qty = (int)$damage['quantity'];

        // If status transitions to Scrapped: deduct from damaged stock
        // If status transitions to Repaired: deduct from damaged stock and add to available stock
        if ($damage['status'] === 'Reported') {
            if ($status === 'Scrapped') {
                $this->stockRepository->adjustStock($pid, $wid, 0, 0, -$qty);
            } elseif ($status === 'Repaired') {
                $this->stockRepository->adjustStock($pid, $wid, $qty, 0, -$qty);
            }
        } elseif ($damage['status'] === 'Scrapped') {
            if ($status === 'Reported') {
                $this->stockRepository->adjustStock($pid, $wid, 0, 0, $qty);
            } elseif ($status === 'Repaired') {
                $this->stockRepository->adjustStock($pid, $wid, $qty, 0, 0);
            }
        } elseif ($damage['status'] === 'Repaired') {
            if ($status === 'Reported') {
                $this->stockRepository->adjustStock($pid, $wid, -$qty, 0, $qty);
            } elseif ($status === 'Scrapped') {
                $this->stockRepository->adjustStock($pid, $wid, -$qty, 0, 0);
            }
        }

        $this->damageRepository->update($id, ['status' => $status], ['%s']);

        AuthService::logActivity(
            get_current_user_id(),
            'DAMAGE_STATUS_UPDATE',
            "Updated status of damage log ID $id to $status"
        );

        return $this->success("Damage log status updated to $status successfully.", $this->damageRepository->findById($id));
    }
}
