<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\PurchaseRepository;
use AccountingManagementApi\Repositories\VendorRepository;
use AccountingManagementApi\Repositories\ItemRepository;
use AccountingManagementApi\Repositories\AccountRepository;
use AccountingManagementApi\Repositories\LedgerRepository;
use AccountingManagementApi\Repositories\GstRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class PurchaseController extends BaseController {
    private $purchaseRepository;
    private $vendorRepository;
    private $itemRepository;
    private $accountRepository;
    private $ledgerRepository;
    private $gstRepository;

    public function __construct() {
        $this->purchaseRepository = new PurchaseRepository();
        $this->vendorRepository = new VendorRepository();
        $this->itemRepository = new ItemRepository();
        $this->accountRepository = new AccountRepository();
        $this->ledgerRepository = new LedgerRepository();
        $this->gstRepository = new GstRepository();
    }

    /**
     * GET /purchases
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'purchase_number', 'purchase_date', 'total_amount', 'created_at'];
        $search_fields = ['purchase_number'];
        
        $extra_filters = [];
        if (isset($params['payment_status'])) {
            $extra_filters['payment_status'] = sanitize_text_field($params['payment_status']);
        }
        if (isset($params['vendor_id'])) {
            $extra_filters['vendor_id'] = intval($params['vendor_id']);
        }

        $results = $this->purchaseRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Include vendor details and item details
        foreach ($results['data'] as &$row) {
            $vendor = $this->vendorRepository->findById($row['vendor_id']);
            $row['vendor_name'] = $vendor ? $vendor['vendor_name'] : 'Unknown';
            $row['items'] = $this->purchaseRepository->getPurchaseItems($row['id']);
        }

        return $this->success('Purchase bills retrieved successfully.', $results);
    }

    /**
     * GET /purchases/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $purchase = $this->purchaseRepository->findById($id);

        if (!$purchase) {
            return $this->error('Purchase bill not found.', [], 404);
        }

        $vendor = $this->vendorRepository->findById($purchase['vendor_id']);
        $purchase['vendor_name'] = $vendor ? $vendor['vendor_name'] : 'Unknown';
        $purchase['vendor_gst'] = $vendor ? $vendor['gst_number'] : '';
        $purchase['items'] = $this->purchaseRepository->getPurchaseItems($id);

        return $this->success('Purchase bill retrieved successfully.', $purchase);
    }

    /**
     * POST /purchases
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['vendor_id']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('Validation failed: vendor_id and items array are required.');
        }

        $vendor_id = intval($params['vendor_id']);
        $vendor = $this->vendorRepository->findById($vendor_id);
        if (!$vendor) {
            return $this->error('Vendor not found.');
        }

        // Generate purchase number
        $purchase_number = 'BILL-ACC-' . sprintf('%04d', rand(1000, 9999));
        while ($this->purchaseRepository->existsPurchaseNumber($purchase_number)) {
            $purchase_number = 'BILL-ACC-' . sprintf('%04d', rand(1000, 9999));
        }

        $purchase_date = sanitize_text_field($params['purchase_date'] ?? date('Y-m-d'));

        // Calculate totals from items
        $subtotal = 0.00;
        $total_cgst = 0.00;
        $total_sgst = 0.00;
        $total_igst = 0.00;

        $purchase_items = [];
        
        foreach ($params['items'] as $item_data) {
            if (empty($item_data['item_id']) || empty($item_data['quantity'])) {
                return $this->error('Each item line must have a valid item_id and quantity.');
            }

            $item_id = intval($item_data['item_id']);
            $qty = intval($item_data['quantity']);
            $item = $this->itemRepository->findById($item_id);
            
            if (!$item) {
                return $this->error("Item ID $item_id not found.");
            }

            $price = floatval($item_data['price'] ?? $item['purchase_price']);
            $gst_rate = floatval($item['gst_percentage']);
            $line_subtotal = $qty * $price;
            
            // Calculate GST
            $line_gst = $line_subtotal * ($gst_rate / 100);
            $line_total = $line_subtotal + $line_gst;

            $subtotal += $line_subtotal;

            // Split CGST/SGST/IGST based on state matching
            $shop_state = get_option('accounting_shop_state', 'Maharashtra');
            if (strcasecmp($vendor['state'], $shop_state) === 0) {
                $line_cgst = $line_gst / 2;
                $line_sgst = $line_gst / 2;
                $line_igst = 0.00;
            } else {
                $line_cgst = 0.00;
                $line_sgst = 0.00;
                $line_igst = $line_gst;
            }

            $total_cgst += $line_cgst;
            $total_sgst += $line_sgst;
            $total_igst += $line_igst;

            $purchase_items[] = [
                'item_id' => $item_id,
                'quantity' => $qty,
                'price' => $price,
                'gst_percentage' => $gst_rate,
                'gst_amount' => $line_gst,
                'total_amount' => $line_total,
                'item_type' => $item['item_type']
            ];
        }

        $total_amount = $subtotal + $total_cgst + $total_sgst + $total_igst;

        // Create main purchase bill record
        $purchases_data = [
            'purchase_number' => $purchase_number,
            'vendor_id' => $vendor_id,
            'purchase_date' => $purchase_date,
            'subtotal' => $subtotal,
            'cgst' => $total_cgst,
            'sgst' => $total_sgst,
            'igst' => $total_igst,
            'total_amount' => $total_amount,
            'payment_status' => sanitize_text_field($params['payment_status'] ?? 'Unpaid')
        ];

        $purchases_formats = ['%s', '%d', '%s', '%f', '%f', '%f', '%f', '%f', '%s'];
        $purchase_id = $this->purchaseRepository->create($purchases_data, $purchases_formats);

        if (!$purchase_id) {
            return $this->error('Failed to create purchase bill.');
        }

        // Insert items
        $this->purchaseRepository->addPurchaseItems($purchase_id, $purchase_items);

        // Update vendor outstanding and items stock quantities (increase stock!)
        $current_outstanding = floatval($vendor['outstanding_amount']);
        $this->vendorRepository->update($vendor_id, ['outstanding_amount' => $current_outstanding + $total_amount], ['%f']);

        foreach ($purchase_items as $pur_item) {
            if ($pur_item['item_type'] === 'Product') {
                $item_record = $this->itemRepository->findById($pur_item['item_id']);
                $new_stock = intval($item_record['stock_quantity']) + $pur_item['quantity'];
                $this->itemRepository->update($pur_item['item_id'], ['stock_quantity' => $new_stock], ['%d']);
            }
        }

        // Double-entry ledger updates:
        // 1. Credit Accounts Payable (2100) -> $total_amount
        $ap_account = $this->accountRepository->findAll(['search' => '2100'])['data'][0] ?? null;
        if ($ap_account) {
            $this->accountRepository->updateBalance($ap_account['id'], $total_amount, 'credit');
            $this->ledgerRepository->create([
                'account_id' => $ap_account['id'],
                'transaction_type' => 'CREDIT',
                'amount' => $total_amount,
                'reference_type' => 'PURCHASE_BILL',
                'reference_id' => $purchase_id,
                'entry_date' => $purchase_date,
                'description' => "Bill $purchase_number from " . $vendor['vendor_name']
            ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
        }

        // 2. Debit Stock/Purchases (Use Rent/Expense account code 5001 or cash for general purchases in seed, but let's debit cash/asset or general expense)
        // Since there is no purchases asset account in seeds, we can debit cash or a dummy purchase account or Rent Expense. Let's look at Chart of accounts seed:
        // 1001 Cash, 1002 HDFC Bank, 1200 AR, 2100 AP, 2200 GST Payable, 4000 Sales Income, 5001 Rent Expense.
        // Let's debit Rent Expense (5001) as a general purchase ledger.
        $expense_account = $this->accountRepository->findAll(['search' => '5001'])['data'][0] ?? null;
        if ($expense_account) {
            $this->accountRepository->updateBalance($expense_account['id'], $subtotal, 'debit');
            $this->ledgerRepository->create([
                'account_id' => $expense_account['id'],
                'transaction_type' => 'DEBIT',
                'amount' => $subtotal,
                'reference_type' => 'PURCHASE_BILL',
                'reference_id' => $purchase_id,
                'entry_date' => $purchase_date,
                'description' => "Cost of goods on Bill $purchase_number"
            ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
        }

        // 3. Debit GST Payable Ledger (2200) (acts as Input Tax Credit - reduce GST payable liability)
        $total_gst = $total_cgst + $total_sgst + $total_igst;
        if ($total_gst > 0) {
            $gst_account = $this->accountRepository->findAll(['search' => '2200'])['data'][0] ?? null;
            if ($gst_account) {
                $this->accountRepository->updateBalance($gst_account['id'], $total_gst, 'debit');
                $this->ledgerRepository->create([
                    'account_id' => $gst_account['id'],
                    'transaction_type' => 'DEBIT',
                    'amount' => $total_gst,
                    'reference_type' => 'PURCHASE_BILL',
                    'reference_id' => $purchase_id,
                    'entry_date' => $purchase_date,
                    'description' => "GST input tax credit on Bill $purchase_number"
                ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
            }

            // Save GST summary record
            $tax_period = date('Y-m', strtotime($purchase_date));
            $this->gstRepository->create([
                'invoice_type' => 'PURCHASES',
                'invoice_id' => $purchase_id,
                'gst_type' => ($total_igst > 0) ? 'IGST' : 'CGST/SGST',
                'gst_rate' => $purchase_items[0]['gst_percentage'], // approximation
                'taxable_amount' => $subtotal,
                'gst_amount' => $total_gst,
                'tax_period' => $tax_period
            ], ['%s', '%d', '%s', '%f', '%f', '%f', '%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'PURCHASE_BILL_CREATE', "Created bill $purchase_number total: $total_amount");

        return $this->success('Purchase bill created successfully.', array_merge(['id' => $purchase_id], $purchases_data), 201);
    }

    /**
     * DELETE /purchases/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $purchase = $this->purchaseRepository->findById($id);

        if (!$purchase) {
            return $this->error('Purchase bill not found.', [], 404);
        }

        // Revert outstanding balance
        $vendor = $this->vendorRepository->findById($purchase['vendor_id']);
        if ($vendor) {
            $new_outstanding = max(0.00, floatval($vendor['outstanding_amount']) - floatval($purchase['total_amount']));
            $this->vendorRepository->update($purchase['vendor_id'], ['outstanding_amount' => $new_outstanding], ['%f']);
        }

        // Revert items stock (decrease stock!)
        $items = $this->purchaseRepository->getPurchaseItems($id);
        foreach ($items as $item) {
            $item_record = $this->itemRepository->findById($item['item_id']);
            if ($item_record && $item_record['item_type'] === 'Product') {
                $new_stock = max(0, intval($item_record['stock_quantity']) - intval($item['quantity']));
                $this->itemRepository->update($item['item_id'], ['stock_quantity' => $new_stock], ['%d']);
            }
        }

        $deleted = $this->purchaseRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete purchase bill.');
        }

        AuthService::logActivity(get_current_user_id(), 'PURCHASE_BILL_DELETE', "Soft deleted bill ID: $id ($purchase[purchase_number])");

        return $this->success('Purchase bill deleted successfully.');
    }
}
