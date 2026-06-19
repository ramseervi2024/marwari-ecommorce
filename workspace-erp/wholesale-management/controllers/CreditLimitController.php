<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\CreditLimitRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class CreditLimitController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new CreditLimitRepository();
    }

    public function getCreditLimits(WP_REST_Request $request) {
        $searchable = ['approval_status'];
        $sortable = ['id', 'dealer_id', 'credit_limit', 'used_credit', 'available_credit', 'approval_status', 'created_at'];
        $result = $this->repo->findAll($request->get_params(), $searchable, $sortable);
        
        // Enrich data with dealer name
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        foreach ($result['data'] as &$cl) {
            $cl['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $cl['dealer_id'])) ?: '';
        }
        return $this->success('Credit limits list.', $result);
    }

    public function getCreditLimit(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        if (!$item) {
            return $this->error('Credit limit record not found.', [], 404);
        }
        global $wpdb;
        $dealers_table = $wpdb->prefix . 'wholesale_dealers';
        $item['dealer_name'] = $wpdb->get_var($wpdb->prepare("SELECT dealer_name FROM $dealers_table WHERE id = %d", $item['dealer_id'])) ?: '';
        return $this->success('Credit limit details.', $item);
    }

    public function createCreditLimit(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['dealer_id'])) {
            return $this->error('Dealer ID is required.');
        }
        $data = [
            'dealer_id'        => (int)$p['dealer_id'],
            'credit_limit'     => isset($p['credit_limit']) ? (float)$p['credit_limit'] : 0.00,
            'used_credit'      => isset($p['used_credit']) ? (float)$p['used_credit'] : 0.00,
            'available_credit' => isset($p['available_credit']) ? (float)$p['available_credit'] : (isset($p['credit_limit']) ? (float)$p['credit_limit'] : 0.00),
            'approval_status'  => $p['approval_status'] ?? 'Pending',
            'approved_by'      => !empty($p['approved_by']) ? (int)$p['approved_by'] : null,
            'approved_at'      => $p['approved_at'] ?? null,
            'notes'            => $p['notes'] ?? '',
        ];
        $formats = ['%d', '%f', '%f', '%f', '%s', '%d', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Credit limit created.', ['id' => $id]) : $this->error('Failed to create credit limit.');
    }

    public function updateCreditLimit(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'dealer_id'        => '%d',
            'credit_limit'     => '%f',
            'used_credit'      => '%f',
            'available_credit' => '%f',
            'approval_status'  => '%s',
            'approved_by'      => '%d',
            'approved_at'      => '%s',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Credit limit updated.') : $this->error('Failed to update credit limit.');
    }

    public function deleteCreditLimit(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Credit limit deleted.') : $this->error('Failed to delete credit limit.');
    }
}
