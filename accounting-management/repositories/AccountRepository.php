<?php
namespace AccountingManagementApi\Repositories;

class AccountRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('accounts', true);
    }

    public function existsAccountCode(string $account_code, ?int $exclude_id = null): bool {
        return $this->exists('account_code', $account_code, $exclude_id);
    }

    /**
     * Update account balance
     */
    public function updateBalance(int $account_id, float $amount, string $type): bool {
        global $wpdb;
        $account = $this->findById($account_id);
        if (!$account) {
            return false;
        }

        $current_balance = (float)$account['balance'];
        $account_type = $account['account_type'];

        // Assets and Expenses increase with Debit, decrease with Credit
        // Liabilities, Equity, and Income increase with Credit, decrease with Debit
        $is_debit_increase = in_array(strtolower($account_type), ['asset', 'expense']);
        
        if (strtolower($type) === 'debit') {
            $new_balance = $is_debit_increase ? ($current_balance + $amount) : ($current_balance - $amount);
        } else {
            $new_balance = $is_debit_increase ? ($current_balance - $amount) : ($current_balance + $amount);
        }

        return $this->update($account_id, ['balance' => $new_balance], ['%f']);
    }
}
