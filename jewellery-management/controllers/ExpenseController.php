<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\ExpenseRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class ExpenseController extends BaseController {
    private $repo;

    public function __construct() {
        $this->repo = new ExpenseRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Expenses retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Expense record not found.', [], 404);
        }
        return $this->success('Expense record retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['expense_type']) || empty($params['amount'])) {
            return $this->error('Validation failed: expense_type and amount are required.');
        }

        $params['expense_type'] = sanitize_text_field($params['expense_type']);
        $params['amount'] = floatval($params['amount']);
        $params['description'] = sanitize_textarea_field($params['description'] ?? '');
        $params['payment_date'] = sanitize_text_field($params['payment_date'] ?? current_time('Y-m-d'));
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create expense record.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_CREATE', "Logged store expense: {$params['expense_type']} of {$params['amount']} INR");

        return $this->success('Expense record created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Expense record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['expense_type'])) $updates['expense_type'] = sanitize_text_field($params['expense_type']);
        if (isset($params['amount'])) $updates['amount'] = floatval($params['amount']);
        if (isset($params['description'])) $updates['description'] = sanitize_textarea_field($params['description']);
        if (isset($params['payment_date'])) $updates['payment_date'] = sanitize_text_field($params['payment_date']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update expense record.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_UPDATE', "Updated expense record ID $id");

        return $this->success('Expense record updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Expense record not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete expense record.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_DELETE', "Deleted expense record ID $id");

        return $this->success('Expense record deleted successfully.');
    }
}
