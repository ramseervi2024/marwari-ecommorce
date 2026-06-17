<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\AccountRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class AccountController extends BaseController {
    private $accountRepository;

    public function __construct() {
        $this->accountRepository = new AccountRepository();
    }

    /**
     * GET /accounts
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'account_code', 'account_name', 'balance', 'created_at'];
        $search_fields = ['account_code', 'account_name', 'account_type'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['account_type'])) {
            $extra_filters['account_type'] = sanitize_text_field($params['account_type']);
        }

        $results = $this->accountRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Chart of Accounts retrieved.', $results);
    }

    /**
     * GET /accounts/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $account = $this->accountRepository->findById($id);

        if (!$account) {
            return $this->error('Account not found.', [], 404);
        }

        return $this->success('Account retrieved successfully.', $account);
    }

    /**
     * POST /accounts
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['account_code']) || empty($params['account_name']) || empty($params['account_type'])) {
            return $this->error('Validation failed: account_code, account_name, and account_type are required.');
        }

        if ($this->accountRepository->existsAccountCode($params['account_code'])) {
            return $this->error('Account code already exists.');
        }

        $data = [
            'account_code' => sanitize_text_field($params['account_code']),
            'account_name' => sanitize_text_field($params['account_name']),
            'account_type' => sanitize_text_field($params['account_type']),
            'balance' => floatval($params['balance'] ?? 0.00),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->accountRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create account.');
        }

        AuthService::logActivity(get_current_user_id(), 'ACCOUNT_CREATE', "Created chart of account: {$data['account_code']} - {$data['account_name']}");

        return $this->success('Account created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /accounts/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $account = $this->accountRepository->findById($id);

        if (!$account) {
            return $this->error('Account not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['account_code'])) {
            if ($this->accountRepository->existsAccountCode($params['account_code'], $id)) {
                return $this->error('Account code already in use.');
            }
            $data['account_code'] = sanitize_text_field($params['account_code']);
            $formats[] = '%s';
        }

        $fields = [
            'account_name' => '%s',
            'account_type' => '%s',
            'balance' => '%f',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%f') {
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

        $updated = $this->accountRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update account details.');
        }

        AuthService::logActivity(get_current_user_id(), 'ACCOUNT_UPDATE', "Updated account ID: $id");

        return $this->success('Account updated successfully.', $this->accountRepository->findById($id));
    }

    /**
     * DELETE /accounts/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $account = $this->accountRepository->findById($id);

        if (!$account) {
            return $this->error('Account not found.', [], 404);
        }

        $deleted = $this->accountRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete account.');
        }

        AuthService::logActivity(get_current_user_id(), 'ACCOUNT_DELETE', "Soft deleted account ID: $id ({$account['account_code']})");

        return $this->success('Account deleted successfully.');
    }
}
