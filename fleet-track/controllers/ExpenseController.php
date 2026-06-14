<?php
namespace FleetTrackPro\Controllers;

use FleetTrackPro\Repositories\ExpenseRepository;
use FleetTrackPro\Services\AuthService;
use WP_REST_Request;

class ExpenseController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new ExpenseRepository();
    }

    /**
     * GET /expenses
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $result = $this->repository->findAllWithDetails($params);
        return $this->success('Expenses fetched successfully', $result);
    }

    /**
     * GET /expenses/{id}
     */
    public function show(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $expense = $this->repository->findExpenseWithDetails($id);

        if (!$expense) {
            return $this->error('Expense not found.', [], 404);
        }

        return $this->success('Expense details fetched successfully', $expense);
    }

    /**
     * POST /expenses
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['expense_type']) || !isset($params['amount']) || empty($params['expense_date'])) {
            return $this->error('Validation failed: expense_type, amount, expense_date are required.');
        }

        $allowed_types = ['Fuel', 'Maintenance', 'Toll', 'Tyre', 'Insurance', 'Permit', 'Salary', 'Repair', 'Parking', 'Miscellaneous'];
        $type = sanitize_text_field($params['expense_type']);
        if (!in_array($type, $allowed_types)) {
            return $this->error("Validation failed: expense_type must be one of: " . implode(', ', $allowed_types));
        }

        $data = [
            'vehicle_id' => !empty($params['vehicle_id']) ? (int)$params['vehicle_id'] : null,
            'driver_id' => !empty($params['driver_id']) ? (int)$params['driver_id'] : null,
            'trip_id' => !empty($params['trip_id']) ? (int)$params['trip_id'] : null,
            'expense_type' => $type,
            'amount' => (float)$params['amount'],
            'expense_date' => sanitize_text_field($params['expense_date']),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%d', '%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s'];
        $expense_id = $this->repository->create($data, $formats);

        if (!$expense_id) {
            return $this->error('Failed to register expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_EXPENSE', "Registered expense ID: $expense_id (Type: $type, Amount: {$data['amount']})");

        return $this->success('Expense created successfully', $this->repository->findExpenseWithDetails($expense_id), 201);
    }

    /**
     * PUT /expenses/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $expense = $this->repository->findById($id);

        if (!$expense) {
            return $this->error('Expense not found.', [], 404);
        }

        $params = $request->get_json_params();
        $update_data = [];
        $formats = [];

        $allowed_fields = [
            'vehicle_id' => '%d',
            'driver_id' => '%d',
            'trip_id' => '%d',
            'expense_type' => '%s',
            'amount' => '%f',
            'expense_date' => '%s',
            'description' => '%s'
        ];

        foreach ($allowed_fields as $field => $format) {
            if (isset($params[$field])) {
                if ($field === 'expense_type') {
                    $allowed_types = ['Fuel', 'Maintenance', 'Toll', 'Tyre', 'Insurance', 'Permit', 'Salary', 'Repair', 'Parking', 'Miscellaneous'];
                    $type = sanitize_text_field($params[$field]);
                    if (!in_array($type, $allowed_types)) {
                        return $this->error("Validation failed: expense_type must be one of: " . implode(', ', $allowed_types));
                    }
                    $update_data[$field] = $type;
                } elseif ($format === '%d') {
                    $update_data[$field] = (int)$params[$field];
                } elseif ($format === '%f') {
                    $update_data[$field] = (float)$params[$field];
                } else {
                    $update_data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($update_data)) {
            return $this->error('No parameters provided for update.');
        }

        $update_data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->repository->update($id, $update_data, $formats);

        if (!$success) {
            return $this->error('Failed to update expense details.');
        }

        AuthService::logActivity(get_current_user_id(), 'UPDATE_EXPENSE', "Updated expense ID: $id");

        return $this->success('Expense updated successfully', $this->repository->findExpenseWithDetails($id));
    }

    /**
     * DELETE /expenses/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $expense = $this->repository->findById($id);

        if (!$expense) {
            return $this->error('Expense not found.', [], 404);
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            return $this->error('Failed to delete expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELETE_EXPENSE', "Soft deleted expense ID: $id");

        return $this->success('Expense soft deleted successfully');
    }
}
