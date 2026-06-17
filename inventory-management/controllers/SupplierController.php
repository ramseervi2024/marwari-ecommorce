<?php
namespace InventoryManagementApi\Controllers;

use InventoryManagementApi\Repositories\SupplierRepository;
use InventoryManagementApi\Services\AuthService;
use WP_REST_Request;

class SupplierController extends BaseController {
    private $supplierRepository;

    public function __construct() {
        $this->supplierRepository = new SupplierRepository();
    }

    /**
     * GET /suppliers
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'supplier_code', 'supplier_name', 'rating', 'created_at'];
        $search_fields = ['supplier_code', 'supplier_name', 'contact_person', 'mobile', 'email', 'gst_number'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->supplierRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Suppliers list retrieved successfully.', $results);
    }

    /**
     * GET /suppliers/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $supplier = $this->supplierRepository->findById($id);

        if (!$supplier) {
            return $this->error('Supplier not found.', [], 404);
        }

        return $this->success('Supplier details retrieved.', $supplier);
    }

    /**
     * POST /suppliers
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['supplier_name'])) {
            return $this->error('Validation failed: supplier_name is required.');
        }

        // Generate supplier code
        $supplier_code = 'SUPP-INV-' . sprintf('%04d', rand(1000, 9999));
        while ($this->supplierRepository->existsSupplierCode($supplier_code)) {
            $supplier_code = 'SUPP-INV-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'supplier_code' => $supplier_code,
            'supplier_name' => sanitize_text_field($params['supplier_name']),
            'contact_person' => sanitize_text_field($params['contact_person'] ?? ''),
            'mobile' => sanitize_text_field($params['mobile'] ?? ''),
            'email' => sanitize_email($params['email'] ?? ''),
            'gst_number' => sanitize_text_field($params['gst_number'] ?? ''),
            'address' => sanitize_textarea_field($params['address'] ?? ''),
            'rating' => floatval($params['rating'] ?? 5.00),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->supplierRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create supplier.');
        }

        AuthService::logActivity(get_current_user_id(), 'SUPPLIER_CREATE', "Created supplier profile $supplier_code - {$data['supplier_name']}");

        return $this->success('Supplier profile created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /suppliers/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $supplier = $this->supplierRepository->findById($id);

        if (!$supplier) {
            return $this->error('Supplier not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['supplier_code'])) {
            if ($this->supplierRepository->existsSupplierCode($params['supplier_code'], $id)) {
                return $this->error('Supplier code already in use.');
            }
            $data['supplier_code'] = sanitize_text_field($params['supplier_code']);
            $formats[] = '%s';
        }

        $fields = [
            'supplier_name' => '%s',
            'contact_person' => '%s',
            'mobile' => '%s',
            'email' => '%s',
            'gst_number' => '%s',
            'address' => '%s',
            'rating' => '%f',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%f') {
                    $data[$field] = floatval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->supplierRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update supplier details.');
        }

        AuthService::logActivity(get_current_user_id(), 'SUPPLIER_UPDATE', "Updated supplier ID: $id");

        return $this->success('Supplier updated successfully.', $this->supplierRepository->findById($id));
    }

    /**
     * DELETE /suppliers/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $supplier = $this->supplierRepository->findById($id);

        if (!$supplier) {
            return $this->error('Supplier not found.', [], 404);
        }

        $deleted = $this->supplierRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete supplier.');
        }

        AuthService::logActivity(get_current_user_id(), 'SUPPLIER_DELETE', "Soft deleted supplier ID: $id ({$supplier['supplier_code']})");

        return $this->success('Supplier deleted successfully.');
    }
}
