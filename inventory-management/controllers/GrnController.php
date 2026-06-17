<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\GrnRepository;
use InventoryManagementApi\Repositories\PurchaseOrderRepository;
use InventoryManagementApi\Repositories\StockRepository;
use InventoryManagementApi\Repositories\InwardRepository;
use InventoryManagementApi\Services\AuthService;
use WP_REST_Request;

class GrnController extends BaseController {
    private $grnRepository;
    private $poRepository;
    private $stockRepository;
    private $inwardRepository;

    public function __construct() {
        $this->grnRepository = new GrnRepository();
        $this->poRepository = new PurchaseOrderRepository();
        $this->stockRepository = new StockRepository();
        $this->inwardRepository = new InwardRepository();
    }

    /**
     * GET /grn
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'grn_number', 'receive_date', 'status'];
        $search_fields = ['grn_number', 'status'];

        $results = $this->grnRepository->findAll($params, $allowed_sorts, $search_fields, []);
        
        foreach ($results['data'] as &$row) {
            $po = $this->poRepository->findById($row['po_id']);
            $row['po_number'] = $po ? $po['po_number'] : 'Unknown';
            $row['items'] = $this->grnRepository->getGrnItems($row['id']);
        }

        return $this->success('Goods Receipt Notes list retrieved.', $results);
    }

    /**
     * POST /grn (Receive items against PO)
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['po_id']) || empty($params['warehouse_id']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('po_id, warehouse_id, and items array are required.');
        }

        $po_id = intval($params['po_id']);
        $warehouse_id = intval($params['warehouse_id']);
        $po = $this->poRepository->findById($po_id);

        if (!$po) {
            return $this->error('Purchase order not found.');
        }

        // Generate GRN number
        $grn_number = 'GRN-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        while ($this->grnRepository->existsGrnNumber($grn_number)) {
            $grn_number = 'GRN-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        }

        $receive_date = sanitize_text_field($params['receive_date'] ?? date('Y-m-d'));

        // Prepare GRN items and validate against PO lines
        $po_items = $this->poRepository->getPoItems($po_id);
        $po_qtys = [];
        foreach ($po_items as $pi) {
            $po_qtys[$pi['product_id']] = (int)$pi['quantity'];
        }

        $grn_items = [];
        $inward_items = [];
        $is_partial = false;

        foreach ($params['items'] as $item) {
            if (empty($item['product_id']) || !isset($item['quantity_received'])) {
                continue;
            }

            $pid = intval($item['product_id']);
            $qty_received = intval($item['quantity_received']);
            $qty_ordered = $po_qtys[$pid] ?? 0;

            if ($qty_ordered === 0) {
                return $this->error("Product ID $pid is not part of this purchase order.");
            }

            if ($qty_received < $qty_ordered) {
                $is_partial = true;
            }

            $grn_items[] = [
                'product_id' => $pid,
                'quantity_ordered' => $qty_ordered,
                'quantity_received' => $qty_received
            ];

            $inward_items[] = [
                'product_id' => $pid,
                'warehouse_id' => $warehouse_id,
                'quantity' => $qty_received,
                'batch_number' => 'BATCH-' . $grn_number . '-' . $pid
            ];
        }

        if (empty($grn_items)) {
            return $this->error('No valid items received.');
        }

        // 1. Create main GRN record
        $grn_id = $this->grnRepository->create([
            'grn_number' => $grn_number,
            'po_id' => $po_id,
            'receive_date' => $receive_date,
            'status' => 'Completed'
        ], ['%s', '%d', '%s', '%s']);

        if (!$grn_id) {
            return $this->error('Failed to create GRN record.');
        }

        // 2. Insert GRN items details
        $this->grnRepository->addGrnItems($grn_id, $grn_items);

        // 3. Create Stock Inward logs to match the transaction
        $inward_id = $this->inwardRepository->create([
            'reference_type' => 'GRN',
            'reference_id' => $grn_id,
            'inward_date' => $receive_date,
            'remarks' => "Stock received against PO $po[po_number] via GRN $grn_number"
        ], ['%s', '%d', '%s', '%s']);

        if ($inward_id) {
            $this->inwardRepository->addInwardItems($inward_id, $inward_items);
        }

        // 4. Update Stock counts (increase available stock!)
        foreach ($inward_items as $in_item) {
            $this->stockRepository->adjustStock($in_item['product_id'], $warehouse_id, $in_item['quantity'], 0, 0);
        }

        // 5. Update PO status to Completed or Partial
        $new_po_status = $is_partial ? 'Approved' : 'Completed'; // keeping it approved if partial to receive rest later
        $this->poRepository->update($po_id, ['status' => $new_po_status], ['%s']);

        AuthService::logActivity(
            get_current_user_id(),
            'GRN_CREATE',
            "Received items for PO ID $po_id via GRN $grn_number"
        );

        return $this->success('Goods Receipt Note created and stock auto-updated successfully.', ['id' => $grn_id, 'grn_number' => $grn_number], 201);
    }
}
