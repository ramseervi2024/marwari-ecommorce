<?php
namespace PharmacyErpApi\Controllers;

use PharmacyErpApi\Repositories\PurchaseRepository;
use PharmacyErpApi\Repositories\BatchRepository;
use WP_REST_Request;

class PurchaseController extends BaseController {
    private $purRepo;
    private $batchRepo;
    public function __construct() {
        $this->purRepo = new PurchaseRepository();
        $this->batchRepo = new BatchRepository();
    }

    public function getPurchases(WP_REST_Request $request) {
        $p = $request->get_params();
        $r = $this->purRepo->findAll($p, ['id','purchase_number','purchase_date'], ['purchase_number','invoice_number']);
        global $wpdb;
        foreach ($r['data'] as &$row) {
            $row['supplier_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pharmacy_suppliers WHERE id=%d", $row['supplier_id']));
        }
        return $this->success('Purchases retrieved.', $r);
    }

    public function getPurchase(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $m = $this->purRepo->findById($id);
        if (!$m) return $this->error('Not found.', [], 404);
        global $wpdb;
        $m['supplier_name'] = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}pharmacy_suppliers WHERE id=%d", $m['supplier_id']));
        $m['items'] = $this->purRepo->getItems($id);
        return $this->success('Purchase retrieved.', $m);
    }

    public function createPurchase(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['supplier_id']) || empty($p['items'])) return $this->error('Supplier and items required.');
        $purchase_number = 'PO-' . strtoupper(uniqid());
        $data = [
            'purchase_number' => $purchase_number,
            'supplier_id' => $p['supplier_id'],
            'purchase_date' => $p['purchase_date'] ?? current_time('Y-m-d'),
            'invoice_number' => $p['invoice_number'] ?? '',
            'subtotal' => $p['subtotal'] ?? 0,
            'discount' => $p['discount'] ?? 0,
            'gst_amount' => $p['gst_amount'] ?? 0,
            'grand_total' => $p['grand_total'] ?? 0,
            'paid_amount' => $p['paid_amount'] ?? 0,
            'status' => $p['status'] ?? 'Pending',
            'notes' => $p['notes'] ?? '',
            'received_by' => get_current_user_id()
        ];
        $id = $this->purRepo->create($data, ['%s','%d','%s','%s','%f','%f','%f','%f','%f','%s','%s','%d']);
        if (!$id) return $this->error('Failed.');

        foreach ($p['items'] as $it) {
            $this->purRepo->insertItem([
                'purchase_id' => $id, 'medicine_id' => $it['medicine_id'],
                'batch_number' => $it['batch_number'] ?? '', 'expiry_date' => $it['expiry_date'] ?? '',
                'quantity' => $it['quantity'], 'free_quantity' => $it['free_quantity'] ?? 0,
                'purchase_price' => $it['purchase_price'] ?? 0, 'mrp' => $it['mrp'] ?? 0,
                'gst_rate' => $it['gst_rate'] ?? 0, 'gst_amount' => $it['gst_amount'] ?? 0,
                'discount' => $it['discount'] ?? 0, 'total' => $it['total'] ?? 0
            ]);
            
            // if Received, add stock
            if ($data['status'] === 'Received' || $data['status'] === 'Completed') {
                $batchId = $this->batchRepo->create([
                    'medicine_id' => $it['medicine_id'],
                    'batch_number' => $it['batch_number'] ?? '',
                    'expiry_date' => $it['expiry_date'] ?? '',
                    'purchase_price' => $it['purchase_price'] ?? 0,
                    'mrp' => $it['mrp'] ?? 0,
                    'quantity' => $it['quantity'] + ($it['free_quantity'] ?? 0),
                    'available_qty' => $it['quantity'] + ($it['free_quantity'] ?? 0)
                ], ['%d','%s','%s','%f','%f','%d','%d']);
            }
        }
        return $this->success('Purchase created.', ['id' => $id, 'purchase_number' => $purchase_number]);
    }

    public function deletePurchase(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->purRepo->delete($id) ? $this->success('Purchase deleted.') : $this->error('Failed.');
    }
}
