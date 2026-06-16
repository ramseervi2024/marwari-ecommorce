<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\PayrollRepository;
use WP_REST_Request;

class PayrollController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new PayrollRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Payroll items retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Payroll item not found.', [], 404);
        }
        return $this->success('Payroll item retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['worker_id']) || empty($params['month_year']) || empty($params['base_salary']) || empty($params['net_salary'])) {
            return $this->error('Validation failed: missing required fields.');
        }

        $params['worker_id'] = intval($params['worker_id'] ?? 0);
        $params['month_year'] = sanitize_text_field($params['month_year'] ?? '');
        $params['base_salary'] = floatval($params['base_salary'] ?? 0);
        $params['allowance'] = floatval($params['allowance'] ?? 0);
        $params['deductions'] = floatval($params['deductions'] ?? 0);
        $params['net_salary'] = floatval($params['net_salary'] ?? 0);
        $params['payment_status'] = sanitize_text_field($params['payment_status'] ?? '');
        $params['payment_date'] = sanitize_text_field($params['payment_date'] ?? '');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create Payroll item.');
        }

        return $this->success('Payroll item created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Payroll item not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['worker_id'])) $updates['worker_id'] = intval($params['worker_id']);
        if (isset($params['month_year'])) $updates['month_year'] = sanitize_text_field($params['month_year']);
        if (isset($params['base_salary'])) $updates['base_salary'] = floatval($params['base_salary']);
        if (isset($params['allowance'])) $updates['allowance'] = floatval($params['allowance']);
        if (isset($params['deductions'])) $updates['deductions'] = floatval($params['deductions']);
        if (isset($params['net_salary'])) $updates['net_salary'] = floatval($params['net_salary']);
        if (isset($params['payment_status'])) $updates['payment_status'] = sanitize_text_field($params['payment_status']);
        if (isset($params['payment_date'])) $updates['payment_date'] = sanitize_text_field($params['payment_date']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update Payroll item.');
        }

        return $this->success('Payroll item updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Payroll item not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete Payroll item.');
        }
        return $this->success('Payroll item deleted successfully.');
    }
}
