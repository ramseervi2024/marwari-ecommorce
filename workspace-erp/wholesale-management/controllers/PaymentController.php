<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\PaymentRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class PaymentController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new PaymentRepository();
    }

    public function getPayments(WP_REST_Request $request) {
        $searchable = ['receipt_number', 'payment_method', 'reference_number', 'status'];
        $sortable = ['id', 'receipt_number', 'payment_date', 'amount', 'status', 'created_at'];
        $result = $this->repo->findAll($request->get_params(), $searchable, $sortable);
        
        // Enrich data with dealer name
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        foreach ($result['data'] as &$p) {
            $p['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $p['dealer_id'])) ?: '';
        }
        return $this->success('Payments list.', $result);
    }

    public function getPayment(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        if (!$item) {
            return $this->error('Payment not found.', [], 404);
        }
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        $item['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $item['dealer_id'])) ?: '';
        return $this->success('Payment details.', $item);
    }

    public function createPayment(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['dealer_id']) || empty($p['payment_date']) || empty($p['amount'])) {
            return $this->error('Dealer ID, payment date, and amount are required.');
        }
        $data = [
            'receipt_number'   => $p['receipt_number'] ?? $this->repo->generateCode('REC-', 'receipt_number'),
            'dealer_id'        => (int)$p['dealer_id'],
            'invoice_id'       => !empty($p['invoice_id']) ? (int)$p['invoice_id'] : null,
            'payment_date'     => $p['payment_date'],
            'amount'           => (float)$p['amount'],
            'payment_method'   => $p['payment_method'] ?? 'Cash',
            'reference_number' => $p['reference_number'] ?? '',
            'bank_name'        => $p['bank_name'] ?? '',
            'cheque_number'    => $p['cheque_number'] ?? '',
            'status'           => $p['status'] ?? 'Received',
            'notes'            => $p['notes'] ?? '',
            'collected_by'     => !empty($p['collected_by']) ? (int)$p['collected_by'] : null,
        ];
        $formats = ['%s', '%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%d'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Payment created.', ['id' => $id, 'receipt_number' => $data['receipt_number']]) : $this->error('Failed to create payment.');
    }

    public function updatePayment(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'dealer_id'        => '%d',
            'invoice_id'       => '%d',
            'payment_date'     => '%s',
            'amount'           => '%f',
            'payment_method'   => '%s',
            'reference_number' => '%s',
            'bank_name'        => '%s',
            'cheque_number'    => '%s',
            'status'           => '%s',
            'notes'            => '%s',
            'collected_by'     => '%d',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Payment updated.') : $this->error('Failed to update payment.');
    }

    public function deletePayment(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Payment deleted.') : $this->error('Failed to delete payment.');
    }
}
