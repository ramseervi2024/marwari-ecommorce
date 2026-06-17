<?php
namespace ConstructionManagementApi\Controllers;

use ConstructionManagementApi\Repositories\SiteExpenseRepository;
use ConstructionManagementApi\Services\AuthService;
use WP_REST_Request;

class ExpenseController extends BaseController {
    private $expenseRepository;

    public function __construct() {
        $this->expenseRepository = new SiteExpenseRepository();
    }

    /**
     * GET /site-expenses
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'project_id', 'expense_type', 'amount', 'expense_date'];
        $search_fields = ['expense_type', 'description', 'approved_by'];

        $extra_filters = [];
        if (isset($params['project_id'])) {
            $extra_filters['project_id'] = intval($params['project_id']);
        }
        if (isset($params['expense_type'])) {
            $extra_filters['expense_type'] = sanitize_text_field($params['expense_type']);
        }

        $results = $this->expenseRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Site expenses retrieved successfully.', $results);
    }

    /**
     * GET /site-expenses/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $expense = $this->expenseRepository->findById($id);

        if (!$expense) {
            return $this->error('Site expense record not found.', [], 404);
        }

        return $this->success('Site expense retrieved successfully.', $expense);
    }

    /**
     * POST /site-expenses
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['project_id']) || empty($params['expense_type']) || empty($params['amount'])) {
            return $this->error('Validation failed: project_id, expense_type, and amount are required.');
        }

        $data = [
            'project_id' => intval($params['project_id']),
            'expense_type' => sanitize_text_field($params['expense_type']),
            'amount' => floatval($params['amount']),
            'expense_date' => !empty($params['expense_date']) ? sanitize_text_field($params['expense_date']) : current_time('Y-m-d'),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'approved_by' => sanitize_text_field($params['approved_by'] ?? '')
        ];

        $formats = ['%d', '%s', '%f', '%s', '%s', '%s'];
        $inserted_id = $this->expenseRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create site expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_CREATE', "Created site expense ID: $inserted_id type: $data[expense_type] amount: $data[amount]");

        return $this->success('Site expense created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /site-expenses/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $expense = $this->expenseRepository->findById($id);

        if (!$expense) {
            return $this->error('Site expense record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['project_id', 'expense_type', 'amount', 'expense_date', 'description', 'approved_by'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'project_id') {
                    $data[$field] = intval($params[$field]);
                    $formats[] = '%d';
                } elseif ($field === 'amount') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } elseif ($field === 'description') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                    $formats[] = '%s';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->expenseRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update site expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_UPDATE', "Updated site expense record ID: $id");

        return $this->success('Site expense updated successfully.', $this->expenseRepository->findById($id));
    }

    /**
     * DELETE /site-expenses/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $expense = $this->expenseRepository->findById($id);

        if (!$expense) {
            return $this->error('Site expense record not found.', [], 404);
        }

        $deleted = $this->expenseRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete site expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_DELETE', "Soft deleted site expense ID: $id ($expense[expense_type]: $expense[amount])");

        return $this->success('Site expense deleted successfully.');
    }
}
