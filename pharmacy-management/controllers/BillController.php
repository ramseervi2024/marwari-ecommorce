<?php
namespace PharmacyErpApi\Controllers;

use PharmacyErpApi\Repositories\BillRepository;
use PharmacyErpApi\Repositories\BatchRepository;
use WP_REST_Request;

class BillController extends BaseController {
    private $billRepo;
    private $batchRepo;
    public function __construct() {
        $this->billRepo = new BillRepository();
        $this->batchRepo = new BatchRepository();
    }

    public function getBills(WP_REST_Request $request) {
        $p = $request->get_params();
        $r = $this->billRepo->findAll($p, ['id','bill_number','bill_date','grand_total'], ['bill_number','customer_name','customer_mobile']);
        return $this->success('Bills retrieved.', $r);
    }

    public function getBill(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $m = $this->billRepo->findById($id);
        if (!$m) return $this->error('Not found.', [], 404);
        $m['items'] = $this->billRepo->getItems($id);
        return $this->success('Bill retrieved.', $m);
    }

    public function createBill(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['items'])) return $this->error('Items required.');
        $bill_number = 'INV-' . strtoupper(uniqid());
        $data = [
            'bill_number' => $bill_number,
            'customer_name' => $p['customer_name'] ?? 'Walk-in Customer',
            'customer_mobile' => $p['customer_mobile'] ?? '',
            'doctor_name' => $p['doctor_name'] ?? '',
            'bill_date' => $p['bill_date'] ?? current_time('Y-m-d'),
            'subtotal' => $p['subtotal'] ?? 0,
            'discount' => $p['discount'] ?? 0,
            'gst_amount' => $p['gst_amount'] ?? 0,
            'grand_total' => $p['grand_total'] ?? 0,
            'paid_amount' => $p['paid_amount'] ?? 0,
            'payment_mode' => $p['payment_mode'] ?? 'Cash',
            'status' => $p['status'] ?? 'Paid',
            'notes' => $p['notes'] ?? '',
            'billed_by' => get_current_user_id()
        ];
        $id = $this->billRepo->create($data, ['%s','%s','%s','%s','%s','%f','%f','%f','%f','%f','%s','%s','%s','%d']);
        if (!$id) return $this->error('Failed.');

        foreach ($p['items'] as $it) {
            $this->billRepo->insertItem([
                'bill_id' => $id, 'medicine_id' => $it['medicine_id'],
                'batch_id' => $it['batch_id'] ?? null, 'batch_number' => $it['batch_number'] ?? '',
                'expiry_date' => $it['expiry_date'] ?? null, 'quantity' => $it['quantity'],
                'unit_price' => $it['unit_price'] ?? 0, 'mrp' => $it['mrp'] ?? 0,
                'discount' => $it['discount'] ?? 0, 'gst_rate' => $it['gst_rate'] ?? 0,
                'gst_amount' => $it['gst_amount'] ?? 0, 'total' => $it['total'] ?? 0
            ]);
            
            // Deduct stock if Paid
            if ($data['status'] === 'Paid' && !empty($it['batch_id'])) {
                $this->batchRepo->deductStock($it['batch_id'], $it['quantity']);
            }
        }
        return $this->success('Bill created.', ['id' => $id, 'bill_number' => $bill_number]);
    }

    public function deleteBill(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        // Ideally should restore stock here, but skipping for MVP
        return $this->billRepo->delete($id) ? $this->success('Bill deleted.') : $this->error('Failed.');
    }
}
