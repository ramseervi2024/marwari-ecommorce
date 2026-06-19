<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\SalesRepRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class SalesRepController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new SalesRepRepository();
    }

    public function getSalesReps(WP_REST_Request $request) {
        $searchable = ['employee_code', 'full_name', 'mobile', 'email', 'territory'];
        $sortable = ['id', 'employee_code', 'full_name', 'target_amount', 'achieved_amount', 'created_at'];
        return $this->success('Sales representatives list.', $this->repo->findAll($request->get_params(), $searchable, $sortable));
    }

    public function getSalesRep(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        return $item ? $this->success('Sales representative details.', $item) : $this->error('Sales representative not found.', [], 404);
    }

    public function createSalesRep(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['full_name'])) {
            return $this->error('Full name is required.');
        }
        $data = [
            'employee_code'   => $p['employee_code'] ?? $this->repo->generateCode('EMP-', 'employee_code'),
            'full_name'       => $p['full_name'],
            'mobile'           => $p['mobile'] ?? '',
            'email'            => $p['email'] ?? '',
            'territory'       => $p['territory'] ?? '',
            'target_amount'   => isset($p['target_amount']) ? (float)$p['target_amount'] : 0.00,
            'achieved_amount' => isset($p['achieved_amount']) ? (float)$p['achieved_amount'] : 0.00,
            'status'          => $p['status'] ?? 'Active',
        ];
        $formats = ['%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Sales representative created.', ['id' => $id, 'employee_code' => $data['employee_code']]) : $this->error('Failed to create sales representative.');
    }

    public function updateSalesRep(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'full_name'       => '%s',
            'mobile'           => '%s',
            'email'            => '%s',
            'territory'       => '%s',
            'target_amount'   => '%f',
            'achieved_amount' => '%f',
            'status'          => '%s',
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
        return $this->repo->update($id, $data, $formats) ? $this->success('Sales representative updated.') : $this->error('Failed to update sales representative.');
    }

    public function deleteSalesRep(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Sales representative deleted.') : $this->error('Failed to delete sales representative.');
    }
}
