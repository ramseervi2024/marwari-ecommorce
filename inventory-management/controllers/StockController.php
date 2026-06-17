<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\StockRepository;
use InventoryManagementApi\Repositories\ProductRepository;
use InventoryManagementApi\Repositories\WarehouseRepository;
use InventoryManagementApi\Repositories\InwardRepository;
use InventoryManagementApi\Repositories\OutwardRepository;
use InventoryManagementApi\Repositories\TransferRepository;
use InventoryManagementApi\Services\AuthService;
use WP_REST_Request;

class StockController extends BaseController {
    private $stockRepository;
    private $productRepository;
    private $warehouseRepository;
    private $inwardRepository;
    private $outwardRepository;
    private $transferRepository;

    public function __construct() {
        $this->stockRepository = new StockRepository();
        $this->productRepository = new ProductRepository();
        $this->warehouseRepository = new WarehouseRepository();
        $this->inwardRepository = new InwardRepository();
        $this->outwardRepository = new OutwardRepository();
        $this->transferRepository = new TransferRepository();
    }

    /**
     * GET /inventory
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'product_id', 'warehouse_id', 'available_stock'];
        $search_fields = [];
        
        $extra_filters = [];
        if (isset($params['product_id'])) {
            $extra_filters['product_id'] = intval($params['product_id']);
        }
        if (isset($params['warehouse_id'])) {
            $extra_filters['warehouse_id'] = intval($params['warehouse_id']);
        }

        $results = $this->stockRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Enrich data with product and warehouse titles
        foreach ($results['data'] as &$row) {
            $p = $this->productRepository->findById($row['product_id']);
            $w = $this->warehouseRepository->findById($row['warehouse_id']);
            $row['product_name'] = $p ? $p['product_name'] : 'Unknown';
            $row['sku'] = $p ? $p['sku'] : '';
            $row['unit'] = $p ? $p['unit'] : 'PCS';
            $row['warehouse_name'] = $w ? $w['warehouse_name'] : 'Unknown';
            $row['warehouse_code'] = $w ? $w['warehouse_code'] : '';
        }

        return $this->success('Stock ledger retrieved.', $results);
    }

    /**
     * POST /inventory (direct manual stock adjustment)
     */
    public function adjust(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['product_id']) || empty($params['warehouse_id']) || !isset($params['available_stock'])) {
            return $this->error('product_id, warehouse_id, and available_stock are required.');
        }

        $pid = intval($params['product_id']);
        $wid = intval($params['warehouse_id']);
        $avail = intval($params['available_stock']);
        $res = intval($params['reserved_stock'] ?? 0);
        $dmg = intval($params['damaged_stock'] ?? 0);

        $p = $this->productRepository->findById($pid);
        $w = $this->warehouseRepository->findById($wid);
        if (!$p || !$w) {
            return $this->error('Product or Warehouse not found.');
        }

        // Adjust stock levels
        $existing = $this->stockRepository->getStockRecord($pid, $wid);
        if ($existing) {
            $diff_avail = $avail - (int)$existing['available_stock'];
            $diff_res = $res - (int)$existing['reserved_stock'];
            $diff_dmg = $dmg - (int)$existing['damaged_stock'];
        } else {
            $diff_avail = $avail;
            $diff_res = $res;
            $diff_dmg = $dmg;
        }

        $updated = $this->stockRepository->adjustStock($pid, $wid, $diff_avail, $diff_res, $diff_dmg);

        if (!$updated) {
            return $this->error('Failed to update stock levels.');
        }

        AuthService::logActivity(
            get_current_user_id(),
            'STOCK_ADJUST',
            "Adjusted stock for product ID $pid in Warehouse ID $wid (Avail: $avail, Res: $res, Dmg: $dmg)"
        );

        return $this->success('Stock levels updated successfully.', $this->stockRepository->getStockRecord($pid, $wid));
    }

    /**
     * GET /stock-inward
     */
    public function getAllInward(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'inward_date', 'created_at'];
        $results = $this->inwardRepository->findAll($params, $allowed_sorts, [], []);
        
        foreach ($results['data'] as &$row) {
            $row['items'] = $this->inwardRepository->getInwardItems($row['id']);
        }

        return $this->success('Stock inward logs retrieved.', $results);
    }

    /**
     * POST /stock-inward
     */
    public function createInward(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['inward_date']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('inward_date and items array are required.');
        }

        $inward_date = sanitize_text_field($params['inward_date']);
        $remarks = sanitize_textarea_field($params['remarks'] ?? '');

        // 1. Create main inward registry
        $inward_id = $this->inwardRepository->create([
            'reference_type' => sanitize_text_field($params['reference_type'] ?? 'Manual Receipt'),
            'reference_id' => isset($params['reference_id']) ? intval($params['reference_id']) : null,
            'inward_date' => $inward_date,
            'remarks' => $remarks
        ], ['%s', '%d', '%s', '%s']);

        if (!$inward_id) {
            return $this->error('Failed to create stock inward entry.');
        }

        // 2. Add item lines and adjust warehouse stock
        $lines = [];
        foreach ($params['items'] as $item) {
            if (empty($item['product_id']) || empty($item['warehouse_id']) || empty($item['quantity'])) {
                continue;
            }

            $pid = intval($item['product_id']);
            $wid = intval($item['warehouse_id']);
            $qty = intval($item['quantity']);
            $batch = sanitize_text_field($item['batch_number'] ?? '');

            $lines[] = [
                'product_id' => $pid,
                'warehouse_id' => $wid,
                'quantity' => $qty,
                'batch_number' => $batch
            ];

            // Increase stock
            $this->stockRepository->adjustStock($pid, $wid, $qty, 0, 0);
        }

        $this->inwardRepository->addInwardItems($inward_id, $lines);

        AuthService::logActivity(
            get_current_user_id(),
            'STOCK_INWARD',
            "Recorded stock inward ID $inward_id on $inward_date"
        );

        return $this->success('Stock inward recorded and inventory auto-updated successfully.', ['id' => $inward_id], 201);
    }

    /**
     * GET /stock-outward
     */
    public function getAllOutward(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'outward_date', 'created_at'];
        $results = $this->outwardRepository->findAll($params, $allowed_sorts, [], []);
        
        foreach ($results['data'] as &$row) {
            $row['items'] = $this->outwardRepository->getOutwardItems($row['id']);
        }

        return $this->success('Stock outward logs retrieved.', $results);
    }

    /**
     * POST /stock-outward
     */
    public function createOutward(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['outward_date']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('outward_date and items array are required.');
        }

        $outward_date = sanitize_text_field($params['outward_date']);
        $remarks = sanitize_textarea_field($params['remarks'] ?? '');

        // 1. Create main outward registry
        $outward_id = $this->outwardRepository->create([
            'reference_type' => sanitize_text_field($params['reference_type'] ?? 'Manual Consumption'),
            'reference_id' => isset($params['reference_id']) ? intval($params['reference_id']) : null,
            'outward_date' => $outward_date,
            'remarks' => $remarks
        ], ['%s', '%d', '%s', '%s']);

        if (!$outward_id) {
            return $this->error('Failed to create stock outward entry.');
        }

        // 2. Add items and deduct stock
        $lines = [];
        foreach ($params['items'] as $item) {
            if (empty($item['product_id']) || empty($item['warehouse_id']) || empty($item['quantity'])) {
                continue;
            }

            $pid = intval($item['product_id']);
            $wid = intval($item['warehouse_id']);
            $qty = intval($item['quantity']);

            // Verify stock is available
            $stock = $this->stockRepository->getStockRecord($pid, $wid);
            $available = $stock ? (int)$stock['available_stock'] : 0;
            if ($available < $qty) {
                // If stock is insufficient, adjust what we can or return error. Let's return error to remain strict.
                return $this->error("Insufficient stock for product ID $pid in warehouse ID $wid. Available: $available, Requested: $qty");
            }

            $lines[] = [
                'product_id' => $pid,
                'warehouse_id' => $wid,
                'quantity' => $qty
            ];

            // Deduct stock
            $this->stockRepository->adjustStock($pid, $wid, -$qty, 0, 0);
        }

        $this->outwardRepository->addOutwardItems($outward_id, $lines);

        AuthService::logActivity(
            get_current_user_id(),
            'STOCK_OUTWARD',
            "Recorded stock outward ID $outward_id on $outward_date"
        );

        return $this->success('Stock outward recorded and inventory auto-deducted successfully.', ['id' => $outward_id], 201);
    }

    /**
     * GET /transfers
     */
    public function getAllTransfers(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'transfer_number', 'transfer_date', 'status', 'created_at'];
        $search_fields = ['transfer_number', 'status'];
        
        $results = $this->transferRepository->findAll($params, $allowed_sorts, $search_fields, []);
        
        foreach ($results['data'] as &$row) {
            $from = $this->warehouseRepository->findById($row['from_warehouse_id']);
            $to = $this->warehouseRepository->findById($row['to_warehouse_id']);
            $row['from_warehouse_name'] = $from ? $from['warehouse_name'] : 'Unknown';
            $row['to_warehouse_name'] = $to ? $to['warehouse_name'] : 'Unknown';
            $row['items'] = $this->transferRepository->getTransferItems($row['id']);
        }

        return $this->success('Inventory transfer logs retrieved.', $results);
    }

    /**
     * POST /transfers (Initiate a stock transfer)
     */
    public function createTransfer(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['from_warehouse_id']) || empty($params['to_warehouse_id']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('from_warehouse_id, to_warehouse_id, and items array are required.');
        }

        $from_id = intval($params['from_warehouse_id']);
        $to_id = intval($params['to_warehouse_id']);

        if ($from_id === $to_id) {
            return $this->error('Source and destination warehouses must be different.');
        }

        $from_wh = $this->warehouseRepository->findById($from_id);
        $to_wh = $this->warehouseRepository->findById($to_id);
        if (!$from_wh || !$to_wh) {
            return $this->error('One or both warehouses do not exist.');
        }

        // Generate transfer number
        $transfer_number = 'XFER-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        while ($this->transferRepository->existsTransferNumber($transfer_number)) {
            $transfer_number = 'XFER-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        }

        $transfer_date = sanitize_text_field($params['transfer_date'] ?? date('Y-m-d'));

        // Validate items and stocks before initiating
        $transfer_items = [];
        foreach ($params['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }

            $pid = intval($item['product_id']);
            $qty = intval($item['quantity']);

            // Check stock at source
            $stock = $this->stockRepository->getStockRecord($pid, $from_id);
            $available = $stock ? (int)$stock['available_stock'] : 0;
            if ($available < $qty) {
                return $this->error("Insufficient stock at source warehouse for product ID $pid. Available: $available, Transfer: $qty");
            }

            $transfer_items[] = [
                'product_id' => $pid,
                'quantity' => $qty
            ];
        }

        if (empty($transfer_items)) {
            return $this->error('No valid items to transfer.');
        }

        // Create transfer header
        $transfer_id = $this->transferRepository->create([
            'transfer_number' => $transfer_number,
            'from_warehouse_id' => $from_id,
            'to_warehouse_id' => $to_id,
            'transfer_date' => $transfer_date,
            'status' => 'Pending' // Requires approval
        ], ['%s', '%d', '%d', '%s', '%s']);

        if (!$transfer_id) {
            return $this->error('Failed to create transfer record.');
        }

        // Add items
        $this->transferRepository->addTransferItems($transfer_id, $transfer_items);

        // Reserve stock at source
        foreach ($transfer_items as $t_item) {
            // Move available to reserved at source
            $this->stockRepository->adjustStock($t_item['product_id'], $from_id, -$t_item['quantity'], $t_item['quantity'], 0);
        }

        AuthService::logActivity(
            get_current_user_id(),
            'TRANSFER_CREATE',
            "Initiated transfer $transfer_number from WH ID $from_id to WH ID $to_id"
        );

        return $this->success('Transfer initiated successfully and requires manager approval.', ['id' => $transfer_id, 'transfer_number' => $transfer_number], 201);
    }

    /**
     * PUT /transfers/{id}/status (Approve/Reject transfer workflow)
     */
    public function updateTransferStatus(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $transfer = $this->transferRepository->findById($id);

        if (!$transfer) {
            return $this->error('Transfer record not found.', [], 404);
        }

        if ($transfer['status'] !== 'Pending') {
            return $this->error('Transfer is already processed.');
        }

        $params = $request->get_json_params();
        $status = sanitize_text_field($params['status'] ?? '');

        if (!in_array($status, ['Approved', 'Rejected'])) {
            return $this->error('Status must be Approved or Rejected.');
        }

        $items = $this->transferRepository->getTransferItems($id);
        $from_id = $transfer['from_warehouse_id'];
        $to_id = $transfer['to_warehouse_id'];

        if ($status === 'Approved') {
            // Deduct reserved at source, increase available at destination
            foreach ($items as $item) {
                $pid = $item['product_id'];
                $qty = (int)$item['quantity'];

                // Deduct reserved at source
                $this->stockRepository->adjustStock($pid, $from_id, 0, -$qty, 0);
                // Increase available at destination
                $this->stockRepository->adjustStock($pid, $to_id, $qty, 0, 0);
            }
        } else {
            // Rejected: Move reserved back to available at source
            foreach ($items as $item) {
                $pid = $item['product_id'];
                $qty = (int)$item['quantity'];

                $this->stockRepository->adjustStock($pid, $from_id, $qty, -$qty, 0);
            }
        }

        $this->transferRepository->update($id, ['status' => $status], ['%s']);

        AuthService::logActivity(
            get_current_user_id(),
            'TRANSFER_STATUS_UPDATE',
            "Updated transfer $transfer[transfer_number] status to $status"
        );

        return $this->success("Transfer request has been $status.");
    }
}
