<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\PurchaseRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class PurchaseController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new PurchaseRepository();
    }

    public function getPurchases(WP_REST_Request $request) {
        $searchable = ['purchase_number', 'status'];
        $sortable = ['id', 'purchase_number', 'purchase_date', 'total_amount', 'net_amount', 'status', 'created_at'];
        $result = $this->repo->findAll($request->get_params(), $searchable, $sortable);
        
        // Enrich data with supplier name
        global $wpdb;
        $suppliers_table = $wpdb->prefix . 'wholesale_suppliers';
        foreach ($result['data'] as &$p) {
            $p['supplier_name'] = $wpdb->get_var($wpdb->prepare("SELECT supplier_name FROM $suppliers_table WHERE id = %d", $p['supplier_id'])) ?: '';
        }
        return $this->success('Purchases list.', $result);
    }

    public function getPurchase(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        if (!$item) {
            return $this->error('Purchase not found.', [], 404);
        }
        global $wpdb;
        $suppliers_table = $wpdb->prefix . 'wholesale_suppliers';
        $item['supplier_name'] = $wpdb->get_var($wpdb->prepare("SELECT supplier_name FROM $suppliers_table WHERE id = %d", $item['supplier_id'])) ?: '';
        return $this->success('Purchase details.', $item);
    }

    public function createPurchase(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['supplier_id']) || empty($p['purchase_date'])) {
            return $this->error('Supplier ID and purchase date are required.');
        }
        $data = [
            'purchase_number' => $p['purchase_number'] ?? $this->repo->generateCode('PO-', 'purchase_number'),
            'supplier_id'     => (int)$p['supplier_id'],
            'warehouse_id'    => !empty($p['warehouse_id']) ? (int)$p['warehouse_id'] : null,
            'purchase_date'   => $p['purchase_date'],
            'total_amount'    => isset($p['total_amount']) ? (float)$p['total_amount'] : 0.00,
            'gst_amount'      => isset($p['gst_amount']) ? (float)$p['gst_amount'] : 0.00,
            'net_amount'      => isset($p['net_amount']) ? (float)$p['net_amount'] : 0.00,
            'status'          => $p['status'] ?? 'Draft',
            'notes'           => $p['notes'] ?? '',
        ];
        $formats = ['%s', '%d', '%d', '%s', '%f', '%f', '%f', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Purchase created.', ['id' => $id, 'purchase_number' => $data['purchase_number']]) : $this->error('Failed to create purchase.');
    }

    public function updatePurchase(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'supplier_id'     => '%d',
            'warehouse_id'    => '%d',
            'purchase_date'   => '%s',
            'total_amount'    => '%f',
            'gst_amount'      => '%f',
            'net_amount'      => '%f',
            'status'          => '%s',
            'notes'            => '%s',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Purchase updated.') : $this->error('Failed to update purchase.');
    }

    public function deletePurchase(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Purchase deleted.') : $this->error('Failed to delete purchase.');
    }
}
