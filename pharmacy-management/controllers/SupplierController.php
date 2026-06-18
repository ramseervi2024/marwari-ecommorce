<?php
namespace PharmacyErpApi\Controllers;

use PharmacyErpApi\Repositories\SupplierRepository;
use WP_REST_Request;

class SupplierController extends BaseController {
    private $supRepo;
    public function __construct() { $this->supRepo = new SupplierRepository(); }

    public function getSuppliers(WP_REST_Request $request) {
        $p = $request->get_params();
        $r = $this->supRepo->findAll($p, ['id','name','supplier_code'], ['name','supplier_code','contact_person','mobile']);
        return $this->success('Suppliers retrieved.', $r);
    }
    public function getSupplier(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $m = $this->supRepo->findById($id);
        return $m ? $this->success('Supplier retrieved.', $m) : $this->error('Not found.', [], 404);
    }
    public function createSupplier(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['supplier_code']) || empty($p['name'])) return $this->error('Code and Name required.');
        $data = [
            'supplier_code' => $p['supplier_code'], 'name' => $p['name'],
            'contact_person' => $p['contact_person'] ?? '', 'mobile' => $p['mobile'] ?? '',
            'email' => $p['email'] ?? '', 'address' => $p['address'] ?? '',
            'city' => $p['city'] ?? '', 'state' => $p['state'] ?? '',
            'gstin' => $p['gstin'] ?? '', 'drug_license' => $p['drug_license'] ?? '',
            'credit_days' => $p['credit_days'] ?? 30
        ];
        $id = $this->supRepo->create($data, ['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d']);
        return $id ? $this->success('Supplier created.', ['id' => $id]) : $this->error('Failed.');
    }
    public function updateSupplier(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = ['supplier_code'=>'%s', 'name'=>'%s', 'contact_person'=>'%s', 'mobile'=>'%s', 'email'=>'%s', 'address'=>'%s', 'city'=>'%s', 'state'=>'%s', 'gstin'=>'%s', 'drug_license'=>'%s', 'credit_days'=>'%d'];
        $data = []; $formats = [];
        foreach ($fields as $f => $fmt) {
            if (isset($p[$f])) { $data[$f] = $p[$f]; $formats[] = $fmt; }
        }
        if (empty($data)) return $this->error('No fields to update.');
        return $this->supRepo->update($id, $data, $formats) ? $this->success('Supplier updated.') : $this->error('Failed.');
    }
    public function deleteSupplier(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->supRepo->delete($id) ? $this->success('Supplier deleted.') : $this->error('Failed.');
    }
}
