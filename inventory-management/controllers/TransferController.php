<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\TransferRepository;
use InventoryManagementApi\Repositories\WarehouseRepository;
use InventoryManagementApi\Repositories\ProductRepository;
use InventoryManagementApi\Repositories\StockRepository;
use InventoryManagementApi\Repositories\InwardRepository;
use InventoryManagementApi\Repositories\OutwardRepository;
use InventoryManagementApi\Services\AuthService;
use WP_REST_Request;

class TransferController extends BaseController {
    private $transferRepository;
    private $warehouseRepository;
    private $productRepository;
    private $stockRepository;
    private $inwardRepository;
    private $outwardRepository;

    public function __construct() {
        $this->transferRepository = new TransferRepository();
        $this->warehouseRepository = new WarehouseRepository();
        $this->productRepository = new ProductRepository();
        $this->stockRepository = new StockRepository();
        $this->inwardRepository = new InwardRepository();
        $this->outwardRepository = new OutwardRepository();
    }

    /**
     * GET /transfers
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'transfer_number', 'transfer_date', 'status'];
        $search_fields = ['transfer_number', 'status'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->transferRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

        foreach ($results['data'] as &$row) {
            $from_wh = $this->warehouseRepository->findById($row['from_warehouse_id']);
            $to_wh = $this->warehouseRepository->findById($row['to_warehouse_id']);
            
            $row['from_warehouse_name'] = $from_wh ? $from_wh['warehouse_name'] : 'Unknown';
            $row['to_warehouse_name'] = $to_wh ? $to_wh['warehouse_name'] : 'Unknown';
            
            $row['items'] = $this->transferRepository->getTransferItems($row['id']);
        }

        return $this->success('Transfers list retrieved.', $results);
    }

    /**
     * GET /transfers/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $transfer = $this->transferRepository->findById($id);

        if (!$transfer) {
            return $this->error('Transfer record not found.', [], 404);
        }

        $from_wh = $this->warehouseRepository->findById($transfer['from_warehouse_id']);
        $to_wh = $this->warehouseRepository->findById($transfer['to_warehouse_id']);
        
        $transfer['from_warehouse_name'] = $from_wh ? $from_wh['warehouse_name'] : 'Unknown';
        $transfer['to_warehouse_name'] = $to_wh ? $to_wh['warehouse_name'] : 'Unknown';
        $transfer['items'] = $this->transferRepository->getTransferItems($id);

        return $this->success('Transfer retrieved.', $transfer);
    }

    /**
     * POST /transfers
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['from_warehouse_id']) || empty($params['to_warehouse_id']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('from_warehouse_id, to_warehouse_id, and items array are required.');
        }

        $from_id = intval($params['from_warehouse_id']);
        $to_id = intval($params['to_warehouse_id']);

        if ($from_id === $to_id) {
            return $this->error('Source and destination warehouses cannot be the same.');
        }

        $from_wh = $this->warehouseRepository->findById($from_id);
        $to_wh = $this->warehouseRepository->findById($to_id);

        if (!$from_wh || !$to_wh) {
            return $this->error('One or both warehouses not found.');
        }

        // Generate transfer number
        $transfer_number = 'TRF-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        while ($this->transferRepository->existsTransferNumber($transfer_number)) {
            $transfer_number = 'TRF-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        }

        $transfer_date = sanitize_text_field($params['transfer_date'] ?? date('Y-m-d'));
        $status = sanitize_text_field($params['status'] ?? 'Pending');

        if (!in_array($status, ['Pending', 'Completed'])) {
            $status = 'Pending';
        }

        // Validate items and verify available stock in source warehouse
        $transfer_items = [];
        foreach ($params['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }

            $pid = intval($item['product_id']);
            $qty = intval($item['quantity']);

            $p = $this->productRepository->findById($pid);
            if (!$p) {
                return $this->error("Product ID $pid not found.");
            }

            $stock = $this->stockRepository->getStockRecord($pid, $from_id);
            $available = $stock ? (int)$stock['available_stock'] : 0;

            if ($available < $qty) {
                return $this->error("Insufficient stock for product {$p['product_name']} in source warehouse. Available: $available, Required: $qty");
            }

            $transfer_items[] = [
                'product_id' => $pid,
                'quantity' => $qty
            ];
        }

        if (empty($transfer_items)) {
            return $this->error('No valid items in transfer list.');
        }

        // 1. Create main transfer record
        $transfer_id = $this->transferRepository->create([
            'transfer_number' => $transfer_number,
            'from_warehouse_id' => $from_id,
            'to_warehouse_id' => $to_id,
            'transfer_date' => $transfer_date,
            'status' => $status
        ], ['%s', '%d', '%d', '%s', '%s']);

        if (!$transfer_id) {
            return $this->error('Failed to create transfer record.');
        }

        // 2. Add transfer items
        $this->transferRepository->addTransferItems($transfer_id, $transfer_items);

        // 3. Deduct stock from source warehouse and log outward
        $outward_id = $this->outwardRepository->create([
            'reference_type' => 'TRANSFER',
            'reference_id' => $transfer_id,
            'outward_date' => $transfer_date,
            'remarks' => "Stock transfer to {$to_wh['warehouse_name']} via Transfer $transfer_number"
        ], ['%s', '%d', '%s', '%s']);

        $outward_items = [];
        foreach ($transfer_items as $item) {
            // Deduct from source warehouse
            $this->stockRepository->adjustStock($item['product_id'], $from_id, -$item['quantity'], 0, 0);

            $outward_items[] = [
                'product_id' => $item['product_id'],
                'warehouse_id' => $from_id,
                'quantity' => $item['quantity']
            ];
        }

        if ($outward_id) {
            $this->outwardRepository->addOutwardItems($outward_id, $outward_items);
        }

        // 4. If Completed, add to destination warehouse and log inward
        if ($status === 'Completed') {
            $inward_id = $this->inwardRepository->create([
                'reference_type' => 'TRANSFER',
                'reference_id' => $transfer_id,
                'inward_date' => $transfer_date,
                'remarks' => "Stock transfer received from {$from_wh['warehouse_name']} via Transfer $transfer_number"
            ], ['%s', '%d', '%s', '%s']);

            $inward_items = [];
            foreach ($transfer_items as $item) {
                // Add to destination warehouse
                $this->stockRepository->adjustStock($item['product_id'], $to_id, $item['quantity'], 0, 0);

                $inward_items[] = [
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $to_id,
                    'quantity' => $item['quantity'],
                    'batch_number' => 'BATCH-TRF-' . $transfer_number . '-' . $item['product_id']
                ];
            }

            if ($inward_id) {
                $this->inwardRepository->addInwardItems($inward_id, $inward_items);
            }
        }

        AuthService::logActivity(
            get_current_user_id(),
            'TRANSFER_CREATE',
            "Created transfer $transfer_number from WH ID $from_id to WH ID $to_id (Status: $status)"
        );

        return $this->success('Transfer initiated successfully.', ['id' => $transfer_id, 'transfer_number' => $transfer_number], 201);
    }

    /**
     * PUT /transfers/:id/status (Mark as Completed)
     */
    public function updateStatus(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $transfer = $this->transferRepository->findById($id);

        if (!$transfer) {
            return $this->error('Transfer record not found.', [], 404);
        }

        if ($transfer['status'] === 'Completed') {
            return $this->error('Transfer is already completed.');
        }

        $params = $request->get_json_params();
        $status = sanitize_text_field($params['status'] ?? '');

        if ($status !== 'Completed') {
            return $this->error('Invalid status transition. Only Completed is supported.');
        }

        $from_wh = $this->warehouseRepository->findById($transfer['from_warehouse_id']);
        $to_wh = $this->warehouseRepository->findById($transfer['to_warehouse_id']);
        $transfer_items = $this->transferRepository->getTransferItems($id);

        // Update transfer status
        $updated = $this->transferRepository->update($id, ['status' => 'Completed'], ['%s']);
        if (!$updated) {
            return $this->error('Failed to update transfer status.');
        }

        // Log inward to destination warehouse and adjust stock
        $inward_id = $this->inwardRepository->create([
            'reference_type' => 'TRANSFER',
            'reference_id' => $id,
            'inward_date' => date('Y-m-d'),
            'remarks' => "Stock transfer received from {$from_wh['warehouse_name']} via Transfer {$transfer['transfer_number']}"
        ], ['%s', '%d', '%s', '%s']);

        $inward_items = [];
        foreach ($transfer_items as $item) {
            // Add to destination warehouse
            $this->stockRepository->adjustStock($item['product_id'], $transfer['to_warehouse_id'], $item['quantity'], 0, 0);

            $inward_items[] = [
                'product_id' => $item['product_id'],
                'warehouse_id' => $transfer['to_warehouse_id'],
                'quantity' => $item['quantity'],
                'batch_number' => 'BATCH-TRF-' . $transfer['transfer_number'] . '-' . $item['product_id']
            ];
        }

        if ($inward_id) {
            $this->inwardRepository->addInwardItems($inward_id, $inward_items);
        }

        AuthService::logActivity(
            get_current_user_id(),
            'TRANSFER_COMPLETE',
            "Completed transfer {$transfer['transfer_number']} to WH ID {$transfer['to_warehouse_id']}"
        );

        return $this->success('Transfer marked as Completed and stock updated.', $this->transferRepository->findById($id));
    }

    /**
     * DELETE /transfers/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $transfer = $this->transferRepository->findById($id);

        if (!$transfer) {
            return $this->error('Transfer not found.', [], 404);
        }

        // Rollback stock adjustments if deleted while Pending?
        // Let's keep it simple: soft delete.
        $deleted = $this->transferRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete transfer.');
        }

        AuthService::logActivity(get_current_user_id(), 'TRANSFER_DELETE', "Soft deleted Transfer ID: $id ({$transfer['transfer_number']})");

        return $this->success('Transfer deleted successfully.');
    }
}
