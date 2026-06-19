<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\OutstandingRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class OutstandingController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new OutstandingRepository();
    }

    public function getOutstandings(WP_REST_Request $request) {
        $searchable = ['invoice_number', 'status'];
        $sortable = ['id', 'invoice_date', 'due_date', 'amount', 'balance', 'days_overdue', 'status', 'created_at'];
        $result = $this->repo->findAll($request->get_params(), $searchable, $sortable);
        
        // Enrich data with dealer name
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        foreach ($result['data'] as &$o) {
            $o['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $o['dealer_id'])) ?: '';
        }
        return $this->success('Outstanding bills list.', $result);
    }

    public function getOutstanding(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        if (!$item) {
            return $this->error('Outstanding record not found.', [], 404);
        }
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        $item['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $item['dealer_id'])) ?: '';
        return $this->success('Outstanding details.', $item);
    }

    public function createOutstanding(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['dealer_id']) || empty($p['amount'])) {
            return $this->error('Dealer ID and amount are required.');
        }
        $data = [
            'dealer_id'      => (int)$p['dealer_id'],
            'invoice_id'     => !empty($p['invoice_id']) ? (int)$p['invoice_id'] : null,
            'invoice_number' => $p['invoice_number'] ?? '',
            'invoice_date'   => $p['invoice_date'] ?? null,
            'due_date'       => $p['due_date'] ?? null,
            'amount'         => (float)$p['amount'],
            'paid_amount'    => isset($p['paid_amount']) ? (float)$p['paid_amount'] : 0.00,
            'balance'        => isset($p['balance']) ? (float)$p['balance'] : (float)$p['amount'],
            'days_overdue'   => isset($p['days_overdue']) ? (int)$p['days_overdue'] : 0,
            'status'         => $p['status'] ?? 'Pending',
        ];
        $formats = ['%d', '%d', '%s', '%s', '%s', '%f', '%f', '%f', '%d', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Outstanding bill created.', ['id' => $id]) : $this->error('Failed to create outstanding bill.');
    }

    public function updateOutstanding(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'dealer_id'      => '%d',
            'invoice_id'     => '%d',
            'invoice_number' => '%s',
            'invoice_date'   => '%s',
            'due_date'       => '%s',
            'amount'         => '%f',
            'paid_amount'    => '%f',
            'balance'        => '%f',
            'days_overdue'   => '%d',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Outstanding bill updated.') : $this->error('Failed to update outstanding bill.');
    }

    public function deleteOutstanding(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Outstanding bill deleted.') : $this->error('Failed to delete outstanding bill.');
    }
}
