<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\PurchaseOrderRepository;
use InventoryManagementApi\Repositories\SupplierRepository;
use InventoryManagementApi\Repositories\ProductRepository;
use InventoryManagementApi\Services\AuthService;
use WP_REST_Request;

class PurchaseOrderController extends BaseController {
    private $poRepository;
    private $supplierRepository;
    private $productRepository;

    public function __construct() {
        $this->poRepository = new PurchaseOrderRepository();
        $this->supplierRepository = new SupplierRepository();
        $this->productRepository = new ProductRepository();
    }

    /**
     * GET /purchase-orders
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'po_number', 'order_date', 'total_amount', 'status'];
        $search_fields = ['po_number', 'status'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['supplier_id'])) {
            $extra_filters['supplier_id'] = intval($params['supplier_id']);
        }

        $results = $this->poRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        foreach ($results['data'] as &$row) {
            $supp = $this->supplierRepository->findById($row['supplier_id']);
            $row['supplier_name'] = $supp ? $supp['supplier_name'] : 'Unknown';
            $row['items'] = $this->poRepository->getPoItems($row['id']);
        }

        return $this->success('Purchase orders retrieved.', $results);
    }

    /**
     * GET /purchase-orders/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $po = $this->poRepository->findById($id);

        if (!$po) {
            return $this->error('Purchase order not found.', [], 404);
        }

        $supp = $this->supplierRepository->findById($po['supplier_id']);
        $po['supplier_name'] = $supp ? $supp['supplier_name'] : 'Unknown';
        $po['items'] = $this->poRepository->getPoItems($id);

        return $this->success('Purchase order retrieved.', $po);
    }

    /**
     * POST /purchase-orders
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['supplier_id']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('Validation failed: supplier_id and items array are required.');
        }

        $supp_id = intval($params['supplier_id']);
        $supp = $this->supplierRepository->findById($supp_id);
        if (!$supp) {
            return $this->error('Supplier not found.');
        }

        // Generate PO number
        $po_number = 'PO-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        while ($this->poRepository->existsPoNumber($po_number)) {
            $po_number = 'PO-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        }

        $order_date = sanitize_text_field($params['order_date'] ?? date('Y-m-d'));

        // Validate items and compute totals
        $po_items = [];
        $total_amount = 0.00;

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

            $price = floatval($item['price'] ?? $p['purchase_price']);
            $line_total = $qty * $price;
            $total_amount += $line_total;

            $po_items[] = [
                'product_id' => $pid,
                'quantity' => $qty,
                'price' => $price
            ];
        }

        if (empty($po_items)) {
            return $this->error('No valid products added to this purchase order.');
        }

        // Create main PO
        $po_id = $this->poRepository->create([
            'po_number' => $po_number,
            'supplier_id' => $supp_id,
            'order_date' => $order_date,
            'total_amount' => $total_amount,
            'status' => 'Pending' // Workflow starts at Pending
        ], ['%s', '%d', '%s', '%f', '%s']);

        if (!$po_id) {
            return $this->error('Failed to create purchase order.');
        }

        // Insert items
        $this->poRepository->addPoItems($po_id, $po_items);

        AuthService::logActivity(
            get_current_user_id(),
            'PO_CREATE',
            "Created Purchase Order $po_number (Total: $total_amount)"
        );

        return $this->success('Purchase order created successfully.', ['id' => $po_id, 'po_number' => $po_number], 201);
    }

    /**
     * PUT /purchase-orders/:id (Update PO details / status workflow)
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $po = $this->poRepository->findById($id);

        if (!$po) {
            return $this->error('Purchase order not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['status'])) {
            $status = sanitize_text_field($params['status']);
            if (!in_array($status, ['Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled'])) {
                return $this->error('Invalid status value.');
            }
            $data['status'] = $status;
            $formats[] = '%s';
        }

        if (isset($params['order_date'])) {
            $data['order_date'] = sanitize_text_field($params['order_date']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->poRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update purchase order.');
        }

        AuthService::logActivity(
            get_current_user_id(),
            'PO_STATUS_UPDATE',
            "Updated status/details of PO ID: $id"
        );

        return $this->success('Purchase order updated successfully.', $this->poRepository->findById($id));
    }

    /**
     * DELETE /purchase-orders/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $po = $this->poRepository->findById($id);

        if (!$po) {
            return $this->error('Purchase order not found.', [], 404);
        }

        $deleted = $this->poRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete purchase order.');
        }

        AuthService::logActivity(get_current_user_id(), 'PO_DELETE', "Soft deleted PO ID: $id ({$po['po_number']})");

        return $this->success('Purchase order deleted successfully.');
    }
}
