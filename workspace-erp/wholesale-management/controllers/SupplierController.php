<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\SupplierRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class SupplierController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new SupplierRepository();
    }

    public function getSuppliers(WP_REST_Request $request) {
        $searchable = ['supplier_code', 'supplier_name', 'contact_person', 'mobile', 'email', 'city', 'state'];
        $sortable = ['id', 'supplier_code', 'supplier_name', 'city', 'state', 'created_at'];
        return $this->success('Suppliers list.', $this->repo->findAll($request->get_params(), $searchable, $sortable));
    }

    public function getSupplier(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        return $item ? $this->success('Supplier details.', $item) : $this->error('Supplier not found.', [], 404);
    }

    public function createSupplier(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['supplier_name'])) {
            return $this->error('Supplier name is required.');
        }
        $data = [
            'supplier_code'  => $p['supplier_code'] ?? $this->repo->generateCode('SUP-', 'supplier_code'),
            'supplier_name'  => $p['supplier_name'],
            'contact_person' => $p['contact_person'] ?? '',
            'mobile'         => $p['mobile'] ?? '',
            'email'          => $p['email'] ?? '',
            'gst_number'     => $p['gst_number'] ?? '',
            'address'        => $p['address'] ?? '',
            'city'           => $p['city'] ?? '',
            'state'          => $p['state'] ?? '',
            'credit_days'    => isset($p['credit_days']) ? (int)$p['credit_days'] : 0,
            'status'         => $p['status'] ?? 'Active',
        ];
        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Supplier created.', ['id' => $id, 'supplier_code' => $data['supplier_code']]) : $this->error('Failed to create supplier.');
    }

    public function updateSupplier(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'supplier_name'  => '%s',
            'contact_person' => '%s',
            'mobile'         => '%s',
            'email'          => '%s',
            'gst_number'     => '%s',
            'address'        => '%s',
            'city'           => '%s',
            'state'          => '%s',
            'credit_days'    => '%d',
            'status'         => '%s',
        ];
        $data = [];
        $formats = [];
        foreach ($fields as $f => $fmt) {
            if (isset($p[$f])) {
                $data[$f] = $p[$f];
                $formats[] = $fmt;
            }
        }
        if (empty($data)) {
            return $this->error('No fields to update.');
        }
        return $this->repo->update($id, $data, $formats) ? $this->success('Supplier updated.') : $this->error('Failed to update supplier.');
    }

    public function deleteSupplier(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Supplier deleted.') : $this->error('Failed to delete supplier.');
    }
}
