<?php
namespace ConstructionManagementApi\Controllers;

use ConstructionManagementApi\Repositories\MaterialRepository;
use ConstructionManagementApi\Repositories\SupplierRepository;
use ConstructionManagementApi\Repositories\PurchaseRepository;
use ConstructionManagementApi\Services\AuthService;
use WP_REST_Request;

class MaterialController extends BaseController {
    private $materialRepository;
    private $supplierRepository;
    private $purchaseRepository;

    public function __construct() {
        $this->materialRepository = new MaterialRepository();
        $this->supplierRepository = new SupplierRepository();
        $this->purchaseRepository = new PurchaseRepository();
    }

    // --- MATERIALS ACTIONS ---

    /**
     * GET /materials
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'material_code', 'material_name', 'available_quantity', 'minimum_stock', 'purchase_price', 'status'];
        $search_fields = ['material_code', 'material_name', 'unit', 'status'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['supplier_id'])) {
            $extra_filters['supplier_id'] = intval($params['supplier_id']);
        }

        $results = $this->materialRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Materials inventory retrieved successfully.', $results);
    }

    /**
     * GET /materials/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $material = $this->materialRepository->findById($id);

        if (!$material) {
            return $this->error('Material not found.', [], 404);
        }

        return $this->success('Material retrieved successfully.', $material);
    }

    /**
     * POST /materials
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['material_name']) || empty($params['unit'])) {
            return $this->error('Validation failed: material_name and unit are required.');
        }

        $material_code = 'MAT-' . strtoupper(substr(sanitize_key($params['material_name']), 0, 3)) . '-' . sprintf('%03d', rand(100, 999));
        while ($this->materialRepository->existsMaterialCode($material_code)) {
            $material_code = 'MAT-' . strtoupper(substr(sanitize_key($params['material_name']), 0, 3)) . '-' . sprintf('%03d', rand(100, 999));
        }

        $data = [
            'material_code' => $material_code,
            'material_name' => sanitize_text_field($params['material_name']),
            'unit' => sanitize_text_field($params['unit']),
            'available_quantity' => isset($params['available_quantity']) ? floatval($params['available_quantity']) : 0.00,
            'minimum_stock' => isset($params['minimum_stock']) ? floatval($params['minimum_stock']) : 0.00,
            'purchase_price' => isset($params['purchase_price']) ? floatval($params['purchase_price']) : 0.00,
            'supplier_id' => !empty($params['supplier_id']) ? intval($params['supplier_id']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%f', '%f', '%f', '%d', '%s'];
        $inserted_id = $this->materialRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create material.');
        }

        AuthService::logActivity(get_current_user_id(), 'MATERIAL_CREATE', "Created material code $material_code ($inserted_id)");

        return $this->success('Material created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /materials/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $material = $this->materialRepository->findById($id);

        if (!$material) {
            return $this->error('Material not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['material_name', 'unit', 'available_quantity', 'minimum_stock', 'purchase_price', 'supplier_id', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'available_quantity' || $field === 'minimum_stock' || $field === 'purchase_price') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } elseif ($field === 'supplier_id') {
                    $data[$field] = intval($params[$field]);
                    $formats[] = '%d';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->materialRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update material.');
        }

        AuthService::logActivity(get_current_user_id(), 'MATERIAL_UPDATE', "Updated material record ID: $id");

        return $this->success('Material updated successfully.', $this->materialRepository->findById($id));
    }

    /**
     * DELETE /materials/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $material = $this->materialRepository->findById($id);

        if (!$material) {
            return $this->error('Material not found.', [], 404);
        }

        $deleted = $this->materialRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete material.');
        }

        AuthService::logActivity(get_current_user_id(), 'MATERIAL_DELETE', "Soft deleted material ID: $id ($material[material_code])");

        return $this->success('Material deleted successfully.');
    }

    // --- SUPPLIERS ACTIONS ---

    /**
     * GET /suppliers
     */
    public function getAllSuppliers(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'supplier_name', 'contact_person', 'rating', 'status'];
        $search_fields = ['supplier_name', 'contact_person', 'mobile', 'email', 'gst_number', 'address'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->supplierRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Suppliers retrieved successfully.', $results);
    }

    /**
     * POST /suppliers
     */
    public function createSupplier(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['supplier_name'])) {
            return $this->error('Validation failed: supplier_name is required.');
        }

        $data = [
            'supplier_name' => sanitize_text_field($params['supplier_name']),
            'contact_person' => sanitize_text_field($params['contact_person'] ?? ''),
            'mobile' => sanitize_text_field($params['mobile'] ?? ''),
            'email' => sanitize_email($params['email'] ?? ''),
            'gst_number' => sanitize_text_field($params['gst_number'] ?? ''),
            'address' => sanitize_textarea_field($params['address'] ?? ''),
            'rating' => isset($params['rating']) ? floatval($params['rating']) : 5.00,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->supplierRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create supplier.');
        }

        AuthService::logActivity(get_current_user_id(), 'SUPPLIER_CREATE', "Created supplier ID: $inserted_id ($data[supplier_name])");

        return $this->success('Supplier created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /suppliers/:id
     */
    public function updateSupplier(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $supplier = $this->supplierRepository->findById($id);

        if (!$supplier) {
            return $this->error('Supplier not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['supplier_name', 'contact_person', 'mobile', 'email', 'gst_number', 'address', 'rating', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'rating') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } elseif ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                    $formats[] = '%s';
                } elseif ($field === 'address') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                    $formats[] = '%s';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->supplierRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update supplier.');
        }

        AuthService::logActivity(get_current_user_id(), 'SUPPLIER_UPDATE', "Updated supplier ID: $id");

        return $this->success('Supplier updated successfully.', $this->supplierRepository->findById($id));
    }

    /**
     * DELETE /suppliers/:id
     */
    public function deleteSupplier(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $supplier = $this->supplierRepository->findById($id);

        if (!$supplier) {
            return $this->error('Supplier not found.', [], 404);
        }

        $deleted = $this->supplierRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete supplier.');
        }

        AuthService::logActivity(get_current_user_id(), 'SUPPLIER_DELETE', "Soft deleted supplier ID: $id ($supplier[supplier_name])");

        return $this->success('Supplier deleted successfully.');
    }

    // --- MATERIAL PURCHASES ACTIONS ---

    /**
     * GET /purchases
     */
    public function getAllPurchases(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'purchase_order_number', 'project_id', 'supplier_id', 'material_id', 'total_amount', 'purchase_date', 'status'];
        $search_fields = ['purchase_order_number', 'status'];

        $extra_filters = [];
        if (isset($params['project_id'])) {
            $extra_filters['project_id'] = intval($params['project_id']);
        }
        if (isset($params['supplier_id'])) {
            $extra_filters['supplier_id'] = intval($params['supplier_id']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->purchaseRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Purchases retrieved successfully.', $results);
    }

    /**
     * POST /purchases
     */
    public function createPurchase(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['project_id']) || empty($params['supplier_id']) || empty($params['material_id']) || empty($params['quantity']) || empty($params['rate'])) {
            return $this->error('Validation failed: project_id, supplier_id, material_id, quantity, and rate are required.');
        }

        $po_number = 'PO-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        while ($this->purchaseRepository->existsPurchaseOrderNumber($po_number)) {
            $po_number = 'PO-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        }

        $qty = floatval($params['quantity']);
        $rate = floatval($params['rate']);
        $gst_amount = ($qty * $rate) * 0.18; // 18% GST default
        $total = ($qty * $rate) + $gst_amount;

        $status = sanitize_text_field($params['status'] ?? 'Pending');

        $data = [
            'purchase_order_number' => $po_number,
            'project_id' => intval($params['project_id']),
            'supplier_id' => intval($params['supplier_id']),
            'material_id' => intval($params['material_id']),
            'quantity' => $qty,
            'rate' => $rate,
            'gst_amount' => $gst_amount,
            'total_amount' => $total,
            'purchase_date' => !empty($params['purchase_date']) ? sanitize_text_field($params['purchase_date']) : current_time('Y-m-d'),
            'status' => $status
        ];

        $formats = ['%s', '%d', '%d', '%d', '%f', '%f', '%f', '%f', '%s', '%s'];
        $inserted_id = $this->purchaseRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create purchase order.');
        }

        // Adjust material stock inventory if PO is Approved immediately
        if ($status === 'Approved') {
            $material = $this->materialRepository->findById($data['material_id']);
            if ($material) {
                $new_qty = floatval($material['available_quantity']) + $qty;
                $this->materialRepository->update($data['material_id'], ['available_quantity' => $new_qty], ['%f']);
            }
        }

        AuthService::logActivity(get_current_user_id(), 'PURCHASE_CREATE', "Created purchase order $po_number ($inserted_id) - status: $status");

        return $this->success('Purchase order created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /purchases/:id
     */
    public function updatePurchase(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $purchase = $this->purchaseRepository->findById($id);

        if (!$purchase) {
            return $this->error('Purchase order not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['project_id', 'supplier_id', 'material_id', 'quantity', 'rate', 'purchase_date', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'project_id' || $field === 'supplier_id' || $field === 'material_id') {
                    $data[$field] = intval($params[$field]);
                    $formats[] = '%d';
                } elseif ($field === 'quantity' || $field === 'rate') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        // Re-calculate pricing if qty or rate is modified
        if (isset($data['quantity']) || isset($data['rate'])) {
            $qty = isset($data['quantity']) ? $data['quantity'] : floatval($purchase['quantity']);
            $rate = isset($data['rate']) ? $data['rate'] : floatval($purchase['rate']);
            $data['gst_amount'] = ($qty * $rate) * 0.18;
            $data['total_amount'] = ($qty * $rate) + $data['gst_amount'];
            $formats[] = '%f';
            $formats[] = '%f';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->purchaseRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update purchase order.');
        }

        // Handle Stock adjustments depending on status changes
        if (isset($data['status']) && $data['status'] !== $purchase['status']) {
            $mat_id = isset($data['material_id']) ? $data['material_id'] : intval($purchase['material_id']);
            $qty = isset($data['quantity']) ? $data['quantity'] : floatval($purchase['quantity']);
            $material = $this->materialRepository->findById($mat_id);
            if ($material) {
                if ($data['status'] === 'Approved' && $purchase['status'] !== 'Approved') {
                    // Added to stock
                    $new_qty = floatval($material['available_quantity']) + $qty;
                    $this->materialRepository->update($mat_id, ['available_quantity' => $new_qty], ['%f']);
                } elseif ($purchase['status'] === 'Approved' && $data['status'] !== 'Approved') {
                    // Subtracted from stock (was approved, now cancelled/pending)
                    $new_qty = floatval($material['available_quantity']) - $qty;
                    $this->materialRepository->update($mat_id, ['available_quantity' => $new_qty], ['%f']);
                }
            }
        }

        AuthService::logActivity(get_current_user_id(), 'PURCHASE_UPDATE', "Updated purchase order ID: $id");

        return $this->success('Purchase order updated successfully.', $this->purchaseRepository->findById($id));
    }

    /**
     * DELETE /purchases/:id
     */
    public function deletePurchase(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $purchase = $this->purchaseRepository->findById($id);

        if (!$purchase) {
            return $this->error('Purchase order not found.', [], 404);
        }

        $deleted = $this->purchaseRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete purchase order.');
        }

        // If it was Approved, subtract quantity from stock since it is deleted
        if ($purchase['status'] === 'Approved') {
            $material = $this->materialRepository->findById($purchase['material_id']);
            if ($material) {
                $new_qty = floatval($material['available_quantity']) - floatval($purchase['quantity']);
                $this->materialRepository->update($purchase['material_id'], ['available_quantity' => $new_qty], ['%f']);
            }
        }

        AuthService::logActivity(get_current_user_id(), 'PURCHASE_DELETE', "Soft deleted purchase order ID: $id ($purchase[purchase_order_number])");

        return $this->success('Purchase order deleted successfully.');
    }
}
