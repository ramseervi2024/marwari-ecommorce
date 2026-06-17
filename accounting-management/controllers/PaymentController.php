<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\PaymentRepository;
use AccountingManagementApi\Repositories\CustomerRepository;
use AccountingManagementApi\Repositories\VendorRepository;
use AccountingManagementApi\Repositories\SalesRepository;
use AccountingManagementApi\Repositories\PurchaseRepository;
use AccountingManagementApi\Repositories\AccountRepository;
use AccountingManagementApi\Repositories\LedgerRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class PaymentController extends BaseController {
    private $paymentRepository;
    private $customerRepository;
    private $vendorRepository;
    private $salesRepository;
    private $purchaseRepository;
    private $accountRepository;
    private $ledgerRepository;

    public function __construct() {
        $this->paymentRepository = new PaymentRepository();
        $this->customerRepository = new CustomerRepository();
        $this->vendorRepository = new VendorRepository();
        $this->salesRepository = new SalesRepository();
        $this->purchaseRepository = new PurchaseRepository();
        $this->accountRepository = new AccountRepository();
        $this->ledgerRepository = new LedgerRepository();
    }

    /**
     * GET /payment
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'amount', 'payment_date', 'created_at'];
        $search_fields = ['payment_type', 'entity_type', 'payment_mode', 'reference_type'];
        
        $extra_filters = [];
        if (isset($params['payment_type'])) {
            $extra_filters['payment_type'] = sanitize_text_field($params['payment_type']);
        }
        if (isset($params['entity_type'])) {
            $extra_filters['entity_type'] = sanitize_text_field($params['entity_type']);
        }

        $results = $this->paymentRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        foreach ($results['data'] as &$row) {
            if ($row['entity_type'] === 'Customer') {
                $customer = $this->customerRepository->findById($row['entity_id']);
                $row['entity_name'] = $customer ? $customer['customer_name'] : 'Unknown';
            } else {
                $vendor = $this->vendorRepository->findById($row['entity_id']);
                $row['entity_name'] = $vendor ? $vendor['vendor_name'] : 'Unknown';
            }
        }

        return $this->success('Payments list retrieved.', $results);
    }

    /**
     * GET /payment/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $payment = $this->paymentRepository->findById($id);

        if (!$payment) {
            return $this->error('Payment record not found.', [], 404);
        }

        return $this->success('Payment details retrieved.', $payment);
    }

    /**
     * POST /payment
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['payment_type']) || empty($params['entity_type']) || empty($params['entity_id']) || empty($params['amount'])) {
            return $this->error('Validation failed: payment_type, entity_type, entity_id, and amount are required.');
        }

        $amount = floatval($params['amount']);
        $payment_type = sanitize_text_field($params['payment_type']);
        $entity_type = sanitize_text_field($params['entity_type']);
        $entity_id = intval($params['entity_id']);
        $payment_mode = sanitize_text_field($params['payment_mode'] ?? 'Bank');
        $payment_date = sanitize_text_field($params['payment_date'] ?? date('Y-m-d'));
        $reference_type = sanitize_text_field($params['reference_type'] ?? '');
        $reference_id = isset($params['reference_id']) ? intval($params['reference_id']) : null;

        // Verify entity and update outstanding
        if ($entity_type === 'Customer') {
            $customer = $this->customerRepository->findById($entity_id);
            if (!$customer) {
                return $this->error('Customer not found.');
            }
            $new_outstanding = max(0.00, floatval($customer['outstanding_amount']) - $amount);
            $this->customerRepository->update($entity_id, ['outstanding_amount' => $new_outstanding], ['%f']);

            // Update sales invoice payment status if referenced
            if ($reference_type === 'SALES_INVOICE' && $reference_id) {
                $invoice = $this->salesRepository->findById($reference_id);
                if ($invoice) {
                    $status = ($amount >= floatval($invoice['total_amount'])) ? 'Paid' : 'Partial';
                    $this->salesRepository->update($reference_id, ['payment_status' => $status], ['%s']);
                }
            }
        } else {
            $vendor = $this->vendorRepository->findById($entity_id);
            if (!$vendor) {
                return $this->error('Vendor not found.');
            }
            $new_outstanding = max(0.00, floatval($vendor['outstanding_amount']) - $amount);
            $this->vendorRepository->update($entity_id, ['outstanding_amount' => $new_outstanding], ['%f']);

            // Update purchase bill payment status if referenced
            if ($reference_type === 'PURCHASE_BILL' && $reference_id) {
                $bill = $this->purchaseRepository->findById($reference_id);
                if ($bill) {
                    $status = ($amount >= floatval($bill['total_amount'])) ? 'Paid' : 'Partial';
                    $this->purchaseRepository->update($reference_id, ['payment_status' => $status], ['%s']);
                }
            }
        }

        $data = [
            'payment_type' => $payment_type,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'amount' => $amount,
            'payment_mode' => $payment_mode,
            'payment_date' => $payment_date
        ];

        $formats = ['%s', '%s', '%d', '%s', '%d', '%f', '%s', '%s'];
        $inserted_id = $this->paymentRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to record payment.');
        }

        // Ledger entries
        $bank_account_code = ($payment_mode === 'Cash') ? '1001' : '1002';
        $bank_account = $this->accountRepository->findAll(['search' => $bank_account_code])['data'][0] ?? null;

        if ($payment_type === 'Collection') {
            // Debit Bank/Cash (1001/1002) / Credit Accounts Receivable (1200)
            if ($bank_account) {
                $this->accountRepository->updateBalance($bank_account['id'], $amount, 'debit');
                $this->ledgerRepository->create([
                    'account_id' => $bank_account['id'],
                    'transaction_type' => 'DEBIT',
                    'amount' => $amount,
                    'reference_type' => 'PAYMENT',
                    'reference_id' => $inserted_id,
                    'entry_date' => $payment_date,
                    'description' => "Collection from customer: " . ($customer['customer_name'] ?? 'Customer')
                ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
            }

            $ar_account = $this->accountRepository->findAll(['search' => '1200'])['data'][0] ?? null;
            if ($ar_account) {
                $this->accountRepository->updateBalance($ar_account['id'], $amount, 'credit');
                $this->ledgerRepository->create([
                    'account_id' => $ar_account['id'],
                    'transaction_type' => 'CREDIT',
                    'amount' => $amount,
                    'reference_type' => 'PAYMENT',
                    'reference_id' => $inserted_id,
                    'entry_date' => $payment_date,
                    'description' => "Settlement of AR for customer: " . ($customer['customer_name'] ?? 'Customer')
                ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
            }
        } else {
            // Credit Bank/Cash (1001/1002) / Debit Accounts Payable (2100)
            if ($bank_account) {
                $this->accountRepository->updateBalance($bank_account['id'], $amount, 'credit');
                $this->ledgerRepository->create([
                    'account_id' => $bank_account['id'],
                    'transaction_type' => 'CREDIT',
                    'amount' => $amount,
                    'reference_type' => 'PAYMENT',
                    'reference_id' => $inserted_id,
                    'entry_date' => $payment_date,
                    'description' => "Payment to vendor: " . ($vendor['vendor_name'] ?? 'Vendor')
                ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
            }

            $ap_account = $this->accountRepository->findAll(['search' => '2100'])['data'][0] ?? null;
            if ($ap_account) {
                $this->accountRepository->updateBalance($ap_account['id'], $amount, 'debit');
                $this->ledgerRepository->create([
                    'account_id' => $ap_account['id'],
                    'transaction_type' => 'DEBIT',
                    'amount' => $amount,
                    'reference_type' => 'PAYMENT',
                    'reference_id' => $inserted_id,
                    'entry_date' => $payment_date,
                    'description' => "Settlement of AP for vendor: " . ($vendor['vendor_name'] ?? 'Vendor')
                ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
            }
        }

        AuthService::logActivity(get_current_user_id(), 'PAYMENT_CREATE', "Recorded payment of amount $amount mode: $payment_mode");

        return $this->success('Payment recorded successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }
}
