<?php
namespace AccountingManagementApi\Repositories;

class LedgerRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('ledger', false);
    }

    /**
     * Get ledger entries for a specific account with sorting and pagination
     */
    public function getAccountLedger(int $account_id, array $params = []): array {
        return $this->findAll($params, ['id', 'entry_date', 'amount'], [], ['account_id' => $account_id]);
    }
}
