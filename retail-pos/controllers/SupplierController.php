<?php
namespace RetailPosApi\Controllers;

use RetailPosApi\Repositories\SupplierRepository;
use RetailPosApi\Services\AuthService;
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
        $allowed_sorts = ['id', 'supplier_name', 'mobile', 'status'];
        $search_fields = ['supplier_name', 'mobile', 'email', 'gst_number'];

        $results = $this->supplierRepository->findAll($params, $allowed_sorts, $search_fields);
        return $this->success('Suppliers retrieved successfully.', $results);
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

        return $this->success('Supplier retrieved successfully.', $supplier);
    }

    /**
     * POST /suppliers
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['supplier_name']) || empty($params['mobile'])) {
            return $this->error('Validation failed: supplier_name and mobile are required.');
        }

        $data = [
            'supplier_name' => sanitize_text_field($params['supplier_name']),
            'mobile' => sanitize_text_field($params['mobile']),
            'email' => sanitize_email($params['email'] ?? ''),
            'address' => sanitize_textarea_field($params['address'] ?? ''),
            'gst_number' => sanitize_text_field($params['gst_number'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $inserted_id = $this->supplierRepository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$inserted_id) {
            return $this->error('Failed to register supplier.');
        }

        AuthService::logActivity(get_current_user_id(), 'SUPPLIER_CREATE', "Registered supplier: $data[supplier_name] ($inserted_id)");

        return $this->success('Supplier registered successfully.', array_merge(['id' => $inserted_id], $data), 201);
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

        $string_fields = ['supplier_name', 'mobile', 'email', 'address', 'gst_number', 'status'];
        foreach ($string_fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                } else if ($field === 'address') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = '%s';
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

        AuthService::logActivity(get_current_user_id(), 'SUPPLIER_DELETE', "Soft deleted supplier ID: $id ($supplier[supplier_name])");

        return $this->success('Supplier deleted successfully.');
    }
}
