<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\JournalRepository;
use AccountingManagementApi\Repositories\AccountRepository;
use AccountingManagementApi\Repositories\LedgerRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class JournalController extends BaseController {
    private $journalRepository;
    private $accountRepository;
    private $ledgerRepository;

    public function __construct() {
        $this->journalRepository = new JournalRepository();
        $this->accountRepository = new AccountRepository();
        $this->ledgerRepository = new LedgerRepository();
    }

    /**
     * GET /journals
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'journal_number', 'transaction_date', 'amount', 'created_at'];
        $search_fields = ['journal_number', 'description'];
        
        $extra_filters = [];

        $results = $this->journalRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Include account details
        foreach ($results['data'] as &$row) {
            $debit = $this->accountRepository->findById($row['debit_account']);
            $credit = $this->accountRepository->findById($row['credit_account']);
            $row['debit_account_name'] = $debit ? "{$debit['account_code']} - {$debit['account_name']}" : 'Unknown';
            $row['credit_account_name'] = $credit ? "{$credit['account_code']} - {$credit['account_name']}" : 'Unknown';
        }

        return $this->success('Journal entries retrieved successfully.', $results);
    }

    /**
     * GET /journals/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $journal = $this->journalRepository->findById($id);

        if (!$journal) {
            return $this->error('Journal entry not found.', [], 404);
        }

        $debit = $this->accountRepository->findById($journal['debit_account']);
        $credit = $this->accountRepository->findById($journal['credit_account']);
        $journal['debit_account_name'] = $debit ? "{$debit['account_code']} - {$debit['account_name']}" : 'Unknown';
        $journal['credit_account_name'] = $credit ? "{$credit['account_code']} - {$credit['account_name']}" : 'Unknown';

        return $this->success('Journal entry retrieved successfully.', $journal);
    }

    /**
     * POST /journals
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['debit_account']) || empty($params['credit_account']) || empty($params['amount'])) {
            return $this->error('Validation failed: debit_account, credit_account, and amount are required.');
        }

        $debit_id = intval($params['debit_account']);
        $credit_id = intval($params['credit_account']);
        $amount = floatval($params['amount']);
        $transaction_date = sanitize_text_field($params['transaction_date'] ?? date('Y-m-d'));

        if ($debit_id === $credit_id) {
            return $this->error('Debit and credit accounts must be different.');
        }

        $debit_acc = $this->accountRepository->findById($debit_id);
        $credit_acc = $this->accountRepository->findById($credit_id);

        if (!$debit_acc || !$credit_acc) {
            return $this->error('One or both accounts do not exist.');
        }

        // Generate journal number
        $journal_number = 'JNL-ACC-' . sprintf('%04d', rand(1000, 9999));
        while ($this->journalRepository->existsJournalNumber($journal_number)) {
            $journal_number = 'JNL-ACC-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'journal_number' => $journal_number,
            'transaction_date' => $transaction_date,
            'debit_account' => $debit_id,
            'credit_account' => $credit_id,
            'amount' => $amount,
            'description' => sanitize_textarea_field($params['description'] ?? '')
        ];

        $formats = ['%s', '%s', '%d', '%d', '%f', '%s'];
        $inserted_id = $this->journalRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create journal entry.');
        }

        // 1. Update balances
        $this->accountRepository->updateBalance($debit_id, $amount, 'debit');
        $this->accountRepository->updateBalance($credit_id, $amount, 'credit');

        // 2. Create Ledger Entries
        $this->ledgerRepository->create([
            'account_id' => $debit_id,
            'transaction_type' => 'DEBIT',
            'amount' => $amount,
            'reference_type' => 'JOURNAL',
            'reference_id' => $inserted_id,
            'entry_date' => $transaction_date,
            'description' => "Journal $journal_number - Debit: " . $data['description']
        ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);

        $this->ledgerRepository->create([
            'account_id' => $credit_id,
            'transaction_type' => 'CREDIT',
            'amount' => $amount,
            'reference_type' => 'JOURNAL',
            'reference_id' => $inserted_id,
            'entry_date' => $transaction_date,
            'description' => "Journal $journal_number - Credit: " . $data['description']
        ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);

        AuthService::logActivity(get_current_user_id(), 'JOURNAL_CREATE', "Created journal entry $journal_number amount: $amount");

        return $this->success('Journal entry recorded successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * DELETE /journals/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $journal = $this->journalRepository->findById($id);

        if (!$journal) {
            return $this->error('Journal entry not found.', [], 404);
        }

        // Revert balances: credit the debited account, debit the credited account
        $this->accountRepository->updateBalance($journal['debit_account'], floatval($journal['amount']), 'credit');
        $this->accountRepository->updateBalance($journal['credit_account'], floatval($journal['amount']), 'debit');

        $deleted = $this->journalRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete journal entry.');
        }

        AuthService::logActivity(get_current_user_id(), 'JOURNAL_DELETE', "Soft deleted journal entry ID: $id ($journal[journal_number])");

        return $this->success('Journal entry deleted successfully.');
    }
}
