<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\BillingRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class BillingController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new BillingRepository();
    }

    public function getBillings(WP_REST_Request $request) {
        $searchable = ['invoice_number', 'invoice_type', 'status'];
        $sortable = ['id', 'invoice_number', 'invoice_date', 'due_date', 'net_amount', 'paid_amount', 'balance', 'status', 'created_at'];
        $result = $this->repo->findAll($request->get_params(), $searchable, $sortable);
        
        // Enrich data with dealer name
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        foreach ($result['data'] as &$b) {
            $b['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $b['dealer_id'])) ?: '';
        }
        return $this->success('Billing list.', $result);
    }

    public function getBilling(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        if (!$item) {
            return $this->error('Invoice details not found.', [], 404);
        }
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        $item['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $item['dealer_id'])) ?: '';
        return $this->success('Invoice details.', $item);
    }

    public function createBilling(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['dealer_id']) || empty($p['invoice_date'])) {
            return $this->error('Dealer ID and invoice date are required.');
        }
        $data = [
            'invoice_number'  => $p['invoice_number'] ?? $this->repo->generateCode('INV-', 'invoice_number'),
            'dealer_id'       => (int)$p['dealer_id'],
            'order_id'        => !empty($p['order_id']) ? (int)$p['order_id'] : null,
            'invoice_date'    => $p['invoice_date'],
            'due_date'        => $p['due_date'] ?? null,
            'subtotal'        => isset($p['subtotal']) ? (float)$p['subtotal'] : 0.00,
            'discount_amount' => isset($p['discount_amount']) ? (float)$p['discount_amount'] : 0.00,
            'gst_amount'      => isset($p['gst_amount']) ? (float)$p['gst_amount'] : 0.00,
            'net_amount'      => isset($p['net_amount']) ? (float)$p['net_amount'] : 0.00,
            'paid_amount'     => isset($p['paid_amount']) ? (float)$p['paid_amount'] : 0.00,
            'balance'         => isset($p['balance']) ? (float)$p['balance'] : (isset($p['net_amount']) ? (float)$p['net_amount'] : 0.00),
            'invoice_type'    => $p['invoice_type'] ?? 'Invoice',
            'status'          => $p['status'] ?? 'Unpaid',
            'notes'           => $p['notes'] ?? '',
        ];
        $formats = ['%s', '%d', '%d', '%s', '%s', '%f', '%f', '%f', '%f', '%f', '%f', '%s', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Invoice created.', ['id' => $id, 'invoice_number' => $data['invoice_number']]) : $this->error('Failed to create invoice.');
    }

    public function updateBilling(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'dealer_id'       => '%d',
            'order_id'        => '%d',
            'invoice_date'    => '%s',
            'due_date'        => '%s',
            'subtotal'        => '%f',
            'discount_amount' => '%f',
            'gst_amount'      => '%f',
            'net_amount'      => '%f',
            'paid_amount'     => '%f',
            'balance'         => '%f',
            'invoice_type'    => '%s',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Invoice updated.') : $this->error('Failed to update invoice.');
    }

    public function deleteBilling(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Invoice deleted.') : $this->error('Failed to delete invoice.');
    }
}
