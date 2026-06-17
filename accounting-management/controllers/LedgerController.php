<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\LedgerRepository;
use AccountingManagementApi\Repositories\AccountRepository;
use WP_REST_Request;

class LedgerController extends BaseController {
    private $ledgerRepository;
    private $accountRepository;

    public function __construct() {
        $this->ledgerRepository = new LedgerRepository();
        $this->accountRepository = new AccountRepository();
    }

    /**
     * GET /ledger
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'entry_date', 'amount'];
        $search_fields = ['description', 'reference_type'];
        
        $extra_filters = [];
        if (isset($params['account_id'])) {
            $extra_filters['account_id'] = intval($params['account_id']);
        }
        if (isset($params['transaction_type'])) {
            $extra_filters['transaction_type'] = sanitize_text_field($params['transaction_type']);
        }

        $results = $this->ledgerRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        foreach ($results['data'] as &$row) {
            $account = $this->accountRepository->findById($row['account_id']);
            $row['account_code'] = $account ? $account['account_code'] : 'Unknown';
            $row['account_name'] = $account ? $account['account_name'] : 'Unknown';
        }

        return $this->success('General Ledger entries retrieved successfully.', $results);
    }
}
