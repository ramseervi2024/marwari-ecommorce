<?php
namespace PharmacyErpApi\Controllers;

use PharmacyErpApi\Repositories\MedicineRepository;
use WP_REST_Request;

class MedicineController extends BaseController {
    private $medRepo;
    public function __construct() { $this->medRepo = new MedicineRepository(); }

    public function getMedicines(WP_REST_Request $request) {
        $p = $request->get_params();
        $r = $this->medRepo->findAll($p, ['id','name','medicine_code'], ['name','medicine_code','generic_name']);
        foreach ($r['data'] as &$m) {
            $stock = $this->medRepo->getStockSummary((int)$m['id']);
            $m['current_stock'] = $stock['current_stock'];
        }
        return $this->success('Medicines retrieved.', $r);
    }
    public function getMedicine(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $m = $this->medRepo->findById($id);
        if (!$m) return $this->error('Not found.', [], 404);
        $stock = $this->medRepo->getStockSummary($id);
        $m['current_stock'] = $stock['current_stock'];
        return $this->success('Medicine retrieved.', $m);
    }
    public function createMedicine(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['medicine_code']) || empty($p['name'])) return $this->error('Code and Name required.');
        if ($this->medRepo->findByCode($p['medicine_code'])) return $this->error('Code already exists.');
        $data = [
            'medicine_code' => $p['medicine_code'], 'name' => $p['name'],
            'generic_name' => $p['generic_name'] ?? '', 'category_id' => $p['category_id'] ?? null,
            'manufacturer' => $p['manufacturer'] ?? '', 'unit' => $p['unit'] ?? 'Strip',
            'hsn_code' => $p['hsn_code'] ?? '', 'gst_rate' => $p['gst_rate'] ?? 5.0,
            'mrp' => $p['mrp'] ?? 0, 'sale_price' => $p['sale_price'] ?? 0,
            'purchase_price' => $p['purchase_price'] ?? 0, 'reorder_level' => $p['reorder_level'] ?? 10,
            'description' => $p['description'] ?? '', 'requires_prescription' => $p['requires_prescription'] ?? 0,
            'is_active' => $p['is_active'] ?? 1
        ];
        $id = $this->medRepo->create($data, ['%s','%s','%s','%d','%s','%s','%s','%f','%f','%f','%f','%d','%s','%d','%d']);
        return $id ? $this->success('Medicine created.', ['id' => $id]) : $this->error('Failed.');
    }
    public function updateMedicine(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = ['medicine_code'=>'%s', 'name'=>'%s', 'generic_name'=>'%s', 'category_id'=>'%d', 'manufacturer'=>'%s', 'unit'=>'%s', 'hsn_code'=>'%s', 'gst_rate'=>'%f', 'mrp'=>'%f', 'sale_price'=>'%f', 'purchase_price'=>'%f', 'reorder_level'=>'%d', 'description'=>'%s', 'requires_prescription'=>'%d', 'is_active'=>'%d'];
        $data = []; $formats = [];
        foreach ($fields as $f => $fmt) {
            if (isset($p[$f])) { $data[$f] = $p[$f]; $formats[] = $fmt; }
        }
        if (empty($data)) return $this->error('No fields to update.');
        return $this->medRepo->update($id, $data, $formats) ? $this->success('Medicine updated.') : $this->error('Failed.');
    }
    public function deleteMedicine(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->medRepo->delete($id) ? $this->success('Medicine deleted.') : $this->error('Failed.');
    }
}
