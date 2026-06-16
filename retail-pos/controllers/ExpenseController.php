<?php
namespace RetailPosApi\Controllers;

use RetailPosApi\Repositories\ExpenseRepository;
use RetailPosApi\Services\AuthService;
use WP_REST_Request;

class ExpenseController extends BaseController {
    private $expenseRepository;

    public function __construct() {
        $this->expenseRepository = new ExpenseRepository();
    }

    /**
     * GET /expenses
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'expense_type', 'amount', 'expense_date'];
        $search_fields = ['expense_type', 'details'];

        $results = $this->expenseRepository->findAll($params, $allowed_sorts, $search_fields);
        return $this->success('Expenses retrieved successfully.', $results);
    }

    /**
     * GET /expenses/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $expense = $this->expenseRepository->findById($id);

        if (!$expense) {
            return $this->error('Expense not found.', [], 404);
        }

        return $this->success('Expense retrieved successfully.', $expense);
    }

    /**
     * POST /expenses
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['expense_type']) || empty($params['amount']) || empty($params['expense_date'])) {
            return $this->error('Validation failed: expense_type, amount, and expense_date are required.');
        }

        $data = [
            'expense_type' => sanitize_text_field($params['expense_type']),
            'amount' => floatval($params['amount']),
            'details' => sanitize_textarea_field($params['details'] ?? ''),
            'expense_date' => sanitize_text_field($params['expense_date'])
        ];

        $inserted_id = $this->expenseRepository->create($data, ['%s', '%f', '%s', '%s']);
        if (!$inserted_id) {
            return $this->error('Failed to log expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_CREATE', "Logged expense type: $data[expense_type] amount: ₹$data[amount]");

        return $this->success('Expense logged successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /expenses/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $expense = $this->expenseRepository->findById($id);

        if (!$expense) {
            return $this->error('Expense not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['expense_type'])) {
            $data['expense_type'] = sanitize_text_field($params['expense_type']);
            $formats[] = '%s';
        }
        if (isset($params['amount'])) {
            $data['amount'] = floatval($params['amount']);
            $formats[] = '%f';
        }
        if (isset($params['details'])) {
            $data['details'] = sanitize_textarea_field($params['details']);
            $formats[] = '%s';
        }
        if (isset($params['expense_date'])) {
            $data['expense_date'] = sanitize_text_field($params['expense_date']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->expenseRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update expense record.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_UPDATE', "Updated expense ID: $id");

        return $this->success('Expense updated successfully.', $this->expenseRepository->findById($id));
    }

    /**
     * DELETE /expenses/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $expense = $this->expenseRepository->findById($id);

        if (!$expense) {
            return $this->error('Expense not found.', [], 404);
        }

        $deleted = $this->expenseRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_DELETE', "Soft deleted expense ID: $id (Type: $expense[expense_type])");

        return $this->success('Expense deleted successfully.');
    }
}
