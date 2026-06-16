<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\SupplierRepository;
use WP_REST_Request;

class SupplierController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new SupplierRepository();
    }

    public function getSuppliers(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $suppliers = $this->repository->all($limit, $offset);
        return $this->success('Suppliers retrieved successfully.', $suppliers);
    }

    public function createSupplier(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['supplier_name']) || empty($params['mobile'])) {
            return $this->error('Validation failed: supplier_name and mobile are required.');
        }

        $data = [
            'supplier_name' => sanitize_text_field($params['supplier_name']),
            'mobile' => sanitize_text_field($params['mobile']),
            'email' => sanitize_email($params['email'] ?? ''),
            'gst_number' => sanitize_text_field($params['gst_number'] ?? ''),
            'address' => sanitize_text_field($params['address'] ?? '')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to create supplier.');
        }

        $data['id'] = $id;
        return $this->success('Supplier registered successfully.', $data, 201);
    }

    public function updateSupplier(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $supplier = $this->repository->find($id);
        if (!$supplier) {
            return $this->error('Supplier not found.', [], 404);
        }

        $data = [];
        if (isset($params['supplier_name'])) $data['supplier_name'] = sanitize_text_field($params['supplier_name']);
        if (isset($params['mobile'])) $data['mobile'] = sanitize_text_field($params['mobile']);
        if (isset($params['email'])) $data['email'] = sanitize_email($params['email']);
        if (isset($params['gst_number'])) $data['gst_number'] = sanitize_text_field($params['gst_number']);
        if (isset($params['address'])) $data['address'] = sanitize_text_field($params['address']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update supplier details.');
        }

        return $this->success('Supplier updated successfully.', array_merge($supplier, $data));
    }

    public function deleteSupplier(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $supplier = $this->repository->find($id);
        if (!$supplier) {
            return $this->error('Supplier not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete supplier.');
        }

        return $this->success('Supplier deleted successfully.');
    }
}
