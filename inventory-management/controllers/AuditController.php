<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\AuditRepository;
use InventoryManagementApi\Repositories\WarehouseRepository;
use InventoryManagementApi\Repositories\StockRepository;
use InventoryManagementApi\Repositories\ProductRepository;
use InventoryManagementApi\Services\AuthService;
use WP_REST_Request;

class AuditController extends BaseController {
    private $auditRepository;
    private $warehouseRepository;
    private $stockRepository;
    private $productRepository;

    public function __construct() {
        $this->auditRepository = new AuditRepository();
        $this->warehouseRepository = new WarehouseRepository();
        $this->stockRepository = new StockRepository();
        $this->productRepository = new ProductRepository();
    }

    /**
     * GET /audits
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'audit_number', 'audit_date', 'status'];
        $search_fields = ['audit_number', 'status'];

        $results = $this->auditRepository->findAll($params, $allowed_sorts, $search_fields, []);
        
        foreach ($results['data'] as &$row) {
            $wh = $this->warehouseRepository->findById($row['warehouse_id']);
            $row['warehouse_name'] = $wh ? $wh['warehouse_name'] : 'Unknown';
            $row['items'] = $this->auditRepository->getAuditItems($row['id']);
        }

        return $this->success('Stock audits list retrieved.', $results);
    }

    /**
     * POST /audits (Schedule/Initiate a physical stock audit)
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['warehouse_id']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('warehouse_id and items array are required.');
        }

        $warehouse_id = intval($params['warehouse_id']);
        $wh = $this->warehouseRepository->findById($warehouse_id);
        if (!$wh) {
            return $this->error('Warehouse not found.');
        }

        // Generate Audit number
        $audit_number = 'AUDIT-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        while ($this->auditRepository->existsAuditNumber($audit_number)) {
            $audit_number = 'AUDIT-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        }

        $audit_date = sanitize_text_field($params['audit_date'] ?? date('Y-m-d'));

        $audit_items = [];
        foreach ($params['items'] as $item) {
            if (empty($item['product_id']) || !isset($item['physical_quantity'])) {
                continue;
            }

            $pid = intval($item['product_id']);
            $p_qty = intval($item['physical_quantity']);

            // Fetch current system stock
            $stock = $this->stockRepository->getStockRecord($pid, $warehouse_id);
            $s_qty = $stock ? (int)$stock['available_stock'] : 0;

            $audit_items[] = [
                'product_id' => $pid,
                'system_quantity' => $s_qty,
                'physical_quantity' => $p_qty
            ];
        }

        if (empty($audit_items)) {
            return $this->error('No valid products to audit.');
        }

        $audit_id = $this->auditRepository->create([
            'audit_number' => $audit_number,
            'warehouse_id' => $warehouse_id,
            'audit_date' => $audit_date,
            'status' => 'Pending' // requires completion/reconciliation
        ], ['%s', '%d', '%s', '%s']);

        if (!$audit_id) {
            return $this->error('Failed to register audit log.');
        }

        $this->auditRepository->addAuditItems($audit_id, $audit_items);

        AuthService::logActivity(
            get_current_user_id(),
            'AUDIT_CREATE',
            "Scheduled stock audit $audit_number in warehouse ID $warehouse_id"
        );

        return $this->success('Stock audit initiated successfully.', ['id' => $audit_id, 'audit_number' => $audit_number], 201);
    }

    /**
     * PUT /audits/{id} (Reconcile / Complete audit)
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $audit = $this->auditRepository->findById($id);

        if (!$audit) {
            return $this->error('Audit not found.', [], 404);
        }

        if ($audit['status'] !== 'Pending') {
            return $this->error('Audit already finalized and reconciled.');
        }

        $params = $request->get_json_params();
        $status = sanitize_text_field($params['status'] ?? '');

        if ($status !== 'Completed') {
            return $this->error('Status must be set to Completed to finalize the audit.');
        }

        // Finalize reconciliation: update warehouse stock count based on variance
        $items = $this->auditRepository->getAuditItems($id);
        $warehouse_id = $audit['warehouse_id'];

        foreach ($items as $item) {
            $pid = $item['product_id'];
            $variance = (int)$item['variance'];

            if ($variance !== 0) {
                // Adjust stock (variance is physical - system)
                // If system was 10, physical was 8, variance is -2. Adjust stock by -2.
                // If system was 10, physical was 12, variance is +2. Adjust stock by +2.
                $this->stockRepository->adjustStock($pid, $warehouse_id, $variance, 0, 0);
            }
        }

        $this->auditRepository->update($id, ['status' => 'Completed'], ['%s']);

        AuthService::logActivity(
            get_current_user_id(),
            'AUDIT_RECONCILE',
            "Reconciled stock for audit $audit[audit_number]"
        );

        return $this->success('Stock audit finalized and warehouse inventory successfully reconciled.');
    }
}
