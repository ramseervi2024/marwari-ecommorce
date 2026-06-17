<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\ExpenseRepository;
use TransportManagementApi\Services\AuthService;
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
        $allowed_sorts = ['id', 'trip_id', 'amount', 'expense_date', 'created_at'];
        $search_fields = ['expense_type', 'description'];
        
        $extra_filters = [];
        if (isset($params['trip_id'])) {
            $extra_filters['trip_id'] = intval($params['trip_id']);
        }
        if (isset($params['expense_type'])) {
            $extra_filters['expense_type'] = sanitize_text_field($params['expense_type']);
        }

        $results = $this->expenseRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
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

        if (empty($params['expense_type']) || empty($params['amount'])) {
            return $this->error('Validation failed: expense_type and amount are required.');
        }

        $data = [
            'trip_id' => !empty($params['trip_id']) ? intval($params['trip_id']) : null,
            'expense_type' => sanitize_text_field($params['expense_type']),
            'amount' => floatval($params['amount']),
            'expense_date' => !empty($params['expense_date']) ? sanitize_text_field($params['expense_date']) : date('Y-m-d'),
            'description' => sanitize_text_field($params['description'] ?? '')
        ];

        $formats = ['%d', '%s', '%f', '%s', '%s'];
        $inserted_id = $this->expenseRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to log expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_CREATE', "Logged expense type {$data['expense_type']} of ₹{$data['amount']}");

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
        
        $fields = [
            'trip_id' => '%d',
            'expense_type' => '%s',
            'amount' => '%f',
            'expense_date' => '%s',
            'description' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $data[$field] = intval($params[$field]);
                } elseif ($format === '%f') {
                    $data[$field] = floatval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->expenseRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_UPDATE', "Updated expense record ID: $id");

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

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_DELETE', "Soft deleted expense record ID: $id");

        return $this->success('Expense deleted successfully.');
    }
}
