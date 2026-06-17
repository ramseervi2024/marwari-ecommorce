<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\ExpenseRepository;
use AccountingManagementApi\Repositories\AccountRepository;
use AccountingManagementApi\Repositories\LedgerRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class ExpenseController extends BaseController {
    private $expenseRepository;
    private $accountRepository;
    private $ledgerRepository;

    public function __construct() {
        $this->expenseRepository = new ExpenseRepository();
        $this->accountRepository = new AccountRepository();
        $this->ledgerRepository = new LedgerRepository();
    }

    /**
     * GET /expenses
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'expense_type', 'amount', 'expense_date', 'created_at'];
        $search_fields = ['expense_type', 'description'];
        
        $extra_filters = [];

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

        $amount = floatval($params['amount']);
        $expense_date = sanitize_text_field($params['expense_date'] ?? date('Y-m-d'));

        $data = [
            'expense_type' => sanitize_text_field($params['expense_type']),
            'amount' => $amount,
            'expense_date' => $expense_date,
            'description' => sanitize_textarea_field($params['description'] ?? '')
        ];

        $formats = ['%s', '%f', '%s', '%s'];
        $inserted_id = $this->expenseRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create expense.');
        }

        // Ledger: Credit Cash (1001) / Debit Rent Expense (5001)
        $cash_account = $this->accountRepository->findAll(['search' => '1001'])['data'][0] ?? null;
        if ($cash_account) {
            $this->accountRepository->updateBalance($cash_account['id'], $amount, 'credit');
            $this->ledgerRepository->create([
                'account_id' => $cash_account['id'],
                'transaction_type' => 'CREDIT',
                'amount' => $amount,
                'reference_type' => 'EXPENSE',
                'reference_id' => $inserted_id,
                'entry_date' => $expense_date,
                'description' => "Paid for expense: " . $data['expense_type']
            ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
        }

        $expense_account = $this->accountRepository->findAll(['search' => '5001'])['data'][0] ?? null;
        if ($expense_account) {
            $this->accountRepository->updateBalance($expense_account['id'], $amount, 'debit');
            $this->ledgerRepository->create([
                'account_id' => $expense_account['id'],
                'transaction_type' => 'DEBIT',
                'amount' => $amount,
                'reference_type' => 'EXPENSE',
                'reference_id' => $inserted_id,
                'entry_date' => $expense_date,
                'description' => "Recorded expense: " . $data['expense_type']
            ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_CREATE', "Recorded expense of type {$data['expense_type']} amount: $amount");

        return $this->success('Expense recorded successfully.', array_merge(['id' => $inserted_id], $data), 201);
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

        // Revert bank balances
        $cash_account = $this->accountRepository->findAll(['search' => '1001'])['data'][0] ?? null;
        if ($cash_account) {
            $this->accountRepository->updateBalance($cash_account['id'], floatval($expense['amount']), 'debit');
        }

        $expense_account = $this->accountRepository->findAll(['search' => '5001'])['data'][0] ?? null;
        if ($expense_account) {
            $this->accountRepository->updateBalance($expense_account['id'], floatval($expense['amount']), 'credit');
        }

        $deleted = $this->expenseRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete expense.');
        }

        AuthService::logActivity(get_current_user_id(), 'EXPENSE_DELETE', "Soft deleted expense ID: $id");

        return $this->success('Expense deleted successfully.');
    }
}
