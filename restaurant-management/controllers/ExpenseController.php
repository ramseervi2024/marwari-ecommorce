<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\ExpenseRepository;
use WP_REST_Request;

class ExpenseController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new ExpenseRepository();
    }

    public function getExpenses(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $expenses = $this->repository->all($limit, $offset);
        return $this->success('Expenses retrieved successfully.', $expenses);
    }

    public function createExpense(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['amount']) || empty($params['expense_type'])) {
            return $this->error('Validation failed: amount and expense_type are required.');
        }

        $data = [
            'amount' => floatval($params['amount']),
            'expense_type' => sanitize_text_field($params['expense_type']),
            'description' => sanitize_textarea_field($params['description'] ?? '')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to log expense.');
        }

        $data['id'] = $id;
        return $this->success('Expense logged successfully.', $data, 201);
    }

    public function updateExpense(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $expense = $this->repository->find($id);
        if (!$expense) {
            return $this->error('Expense not found.', [], 404);
        }

        $data = [];
        if (isset($params['amount'])) $data['amount'] = floatval($params['amount']);
        if (isset($params['expense_type'])) $data['expense_type'] = sanitize_text_field($params['expense_type']);
        if (isset($params['description'])) $data['description'] = sanitize_textarea_field($params['description']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update expense details.');
        }

        return $this->success('Expense updated successfully.', array_merge($expense, $data));
    }

    public function deleteExpense(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $expense = $this->repository->find($id);
        if (!$expense) {
            return $this->error('Expense not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete expense record.');
        }

        return $this->success('Expense record deleted successfully.');
    }
}
