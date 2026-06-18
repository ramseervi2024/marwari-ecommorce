<?php
namespace PharmacyErpApi\Controllers;

use PharmacyErpApi\Repositories\BatchRepository;
use WP_REST_Request;

class BatchController extends BaseController {
    private $batchRepo;
    public function __construct() { $this->batchRepo = new BatchRepository(); }

    public function getBatches(WP_REST_Request $request) {
        $p = $request->get_params();
        if (!empty($p['medicine_id'])) {
            $r = ['data' => $this->batchRepo->getByMedicine((int)$p['medicine_id']), 'total' => 0, 'page' => 1, 'limit' => 100, 'pages' => 1];
        } else {
            $r = $this->batchRepo->findAll($p, ['id','batch_number','expiry_date'], ['batch_number']);
            // join medicines to get names
            global $wpdb;
            foreach ($r['data'] as &$b) {
                $b['medicine_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pharmacy_medicines WHERE id=%d", $b['medicine_id']));
            }
        }
        return $this->success('Batches retrieved.', $r);
    }
    public function getExpiryAlerts(WP_REST_Request $request) {
        $days = (int)$request->get_param('days') ?: 30;
        return $this->success('Alerts.', $this->batchRepo->getExpiryAlerts($days));
    }
    public function getExpired(WP_REST_Request $request) {
        return $this->success('Expired.', $this->batchRepo->getExpired());
    }
    public function updateBatch(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = ['batch_number'=>'%s', 'manufacturer'=>'%s', 'manufacturing_date'=>'%s', 'expiry_date'=>'%s', 'purchase_price'=>'%f', 'mrp'=>'%f', 'quantity'=>'%d', 'available_qty'=>'%d'];
        $data = []; $formats = [];
        foreach ($fields as $f => $fmt) {
            if (isset($p[$f])) { $data[$f] = $p[$f]; $formats[] = $fmt; }
        }
        if (empty($data)) return $this->error('No fields to update.');
        return $this->batchRepo->update($id, $data, $formats) ? $this->success('Batch updated.') : $this->error('Failed.');
    }
    public function deleteBatch(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->batchRepo->delete($id) ? $this->success('Batch deleted.') : $this->error('Failed.');
    }
}
