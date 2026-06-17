<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\SalesRepository;
use AccountingManagementApi\Repositories\CustomerRepository;
use AccountingManagementApi\Repositories\ItemRepository;
use AccountingManagementApi\Repositories\AccountRepository;
use AccountingManagementApi\Repositories\LedgerRepository;
use AccountingManagementApi\Repositories\GstRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class SalesController extends BaseController {
    private $salesRepository;
    private $customerRepository;
    private $itemRepository;
    private $accountRepository;
    private $ledgerRepository;
    private $gstRepository;

    public function __construct() {
        $this->salesRepository = new SalesRepository();
        $this->customerRepository = new CustomerRepository();
        $this->itemRepository = new ItemRepository();
        $this->accountRepository = new AccountRepository();
        $this->ledgerRepository = new LedgerRepository();
        $this->gstRepository = new GstRepository();
    }

    /**
     * GET /sales
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'invoice_number', 'invoice_date', 'total_amount', 'created_at'];
        $search_fields = ['invoice_number'];
        
        $extra_filters = [];
        if (isset($params['payment_status'])) {
            $extra_filters['payment_status'] = sanitize_text_field($params['payment_status']);
        }
        if (isset($params['customer_id'])) {
            $extra_filters['customer_id'] = intval($params['customer_id']);
        }

        $results = $this->salesRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Include customer details and item details
        foreach ($results['data'] as &$row) {
            $customer = $this->customerRepository->findById($row['customer_id']);
            $row['customer_name'] = $customer ? $customer['customer_name'] : 'Unknown';
            $row['items'] = $this->salesRepository->getInvoiceItems($row['id']);
        }

        return $this->success('Sales invoices retrieved successfully.', $results);
    }

    /**
     * GET /sales/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->salesRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        $customer = $this->customerRepository->findById($invoice['customer_id']);
        $invoice['customer_name'] = $customer ? $customer['customer_name'] : 'Unknown';
        $invoice['customer_gst'] = $customer ? $customer['gst_number'] : '';
        $invoice['items'] = $this->salesRepository->getInvoiceItems($id);

        return $this->success('Invoice retrieved successfully.', $invoice);
    }

    /**
     * POST /sales
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['customer_id']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('Validation failed: customer_id and items array are required.');
        }

        $customer_id = intval($params['customer_id']);
        $customer = $this->customerRepository->findById($customer_id);
        if (!$customer) {
            return $this->error('Customer not found.');
        }

        // Generate invoice number
        $invoice_number = 'INV-ACC-' . sprintf('%04d', rand(1000, 9999));
        while ($this->salesRepository->existsInvoiceNumber($invoice_number)) {
            $invoice_number = 'INV-ACC-' . sprintf('%04d', rand(1000, 9999));
        }

        $invoice_date = sanitize_text_field($params['invoice_date'] ?? date('Y-m-d'));

        // Calculate totals from items
        $subtotal = 0.00;
        $total_cgst = 0.00;
        $total_sgst = 0.00;
        $total_igst = 0.00;
        $discount = floatval($params['discount'] ?? 0.00);

        $invoice_items = [];
        
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

            $price = floatval($item_data['price'] ?? $item['selling_price']);
            $gst_rate = floatval($item['gst_percentage']);
            $line_subtotal = $qty * $price;
            
            // Calculate GST
            $line_gst = $line_subtotal * ($gst_rate / 100);
            $line_total = $line_subtotal + $line_gst;

            $subtotal += $line_subtotal;

            // Split CGST/SGST/IGST based on state matching
            // Assuming customer is in same state if state matches shop state (e.g. Maharashtra)
            $shop_state = get_option('accounting_shop_state', 'Maharashtra');
            if (strcasecmp($customer['state'], $shop_state) === 0) {
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

            $invoice_items[] = [
                'item_id' => $item_id,
                'quantity' => $qty,
                'price' => $price,
                'gst_percentage' => $gst_rate,
                'gst_amount' => $line_gst,
                'total_amount' => $line_total,
                'item_type' => $item['item_type']
            ];
        }

        $total_amount = $subtotal + $total_cgst + $total_sgst + $total_igst - $discount;

        // Create main invoice record
        $sales_data = [
            'invoice_number' => $invoice_number,
            'customer_id' => $customer_id,
            'invoice_date' => $invoice_date,
            'subtotal' => $subtotal,
            'cgst' => $total_cgst,
            'sgst' => $total_sgst,
            'igst' => $total_igst,
            'discount' => $discount,
            'total_amount' => $total_amount,
            'payment_status' => sanitize_text_field($params['payment_status'] ?? 'Unpaid')
        ];

        $sales_formats = ['%s', '%d', '%s', '%f', '%f', '%f', '%f', '%f', '%f', '%s'];
        $sale_id = $this->salesRepository->create($sales_data, $sales_formats);

        if (!$sale_id) {
            return $this->error('Failed to create sales invoice.');
        }

        // Insert items
        $this->salesRepository->addInvoiceItems($sale_id, $invoice_items);

        // Update customer outstanding and items stock quantities
        $current_outstanding = floatval($customer['outstanding_amount']);
        $this->customerRepository->update($customer_id, ['outstanding_amount' => $current_outstanding + $total_amount], ['%f']);

        foreach ($invoice_items as $inv_item) {
            if ($inv_item['item_type'] === 'Product') {
                $item_record = $this->itemRepository->findById($inv_item['item_id']);
                $new_stock = intval($item_record['stock_quantity']) - $inv_item['quantity'];
                $this->itemRepository->update($inv_item['item_id'], ['stock_quantity' => $new_stock], ['%d']);
            }
        }

        // Double-entry ledger updates:
        // 1. Debit Accounts Receivable (1200) -> $total_amount
        $ar_account = $this->accountRepository->findAll(['search' => '1200'])['data'][0] ?? null;
        if ($ar_account) {
            $this->accountRepository->updateBalance($ar_account['id'], $total_amount, 'debit');
            $this->ledgerRepository->create([
                'account_id' => $ar_account['id'],
                'transaction_type' => 'DEBIT',
                'amount' => $total_amount,
                'reference_type' => 'SALES_INVOICE',
                'reference_id' => $sale_id,
                'entry_date' => $invoice_date,
                'description' => "Invoice $invoice_number for " . $customer['customer_name']
            ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
        }

        // 2. Credit Sales Income (4000) -> $subtotal - $discount
        $sales_account = $this->accountRepository->findAll(['search' => '4000'])['data'][0] ?? null;
        $sales_revenue = $subtotal - $discount;
        if ($sales_account) {
            $this->accountRepository->updateBalance($sales_account['id'], $sales_revenue, 'credit');
            $this->ledgerRepository->create([
                'account_id' => $sales_account['id'],
                'transaction_type' => 'CREDIT',
                'amount' => $sales_revenue,
                'reference_type' => 'SALES_INVOICE',
                'reference_id' => $sale_id,
                'entry_date' => $invoice_date,
                'description' => "Revenue from Invoice $invoice_number"
            ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
        }

        // 3. Credit GST Payable Ledger (2200) -> $total_gst
        $total_gst = $total_cgst + $total_sgst + $total_igst;
        if ($total_gst > 0) {
            $gst_account = $this->accountRepository->findAll(['search' => '2200'])['data'][0] ?? null;
            if ($gst_account) {
                $this->accountRepository->updateBalance($gst_account['id'], $total_gst, 'credit');
                $this->ledgerRepository->create([
                    'account_id' => $gst_account['id'],
                    'transaction_type' => 'CREDIT',
                    'amount' => $total_gst,
                    'reference_type' => 'SALES_INVOICE',
                    'reference_id' => $sale_id,
                    'entry_date' => $invoice_date,
                    'description' => "GST collected on Invoice $invoice_number"
                ], ['%d', '%s', '%f', '%s', '%d', '%s', '%s']);
            }

            // Save GST summary record
            $tax_period = date('Y-m', strtotime($invoice_date));
            $this->gstRepository->create([
                'invoice_type' => 'SALES',
                'invoice_id' => $sale_id,
                'gst_type' => ($total_igst > 0) ? 'IGST' : 'CGST/SGST',
                'gst_rate' => $invoice_items[0]['gst_percentage'], // approximation
                'taxable_amount' => $subtotal - $discount,
                'gst_amount' => $total_gst,
                'tax_period' => $tax_period
            ], ['%s', '%d', '%s', '%f', '%f', '%f', '%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'SALES_INVOICE_CREATE', "Created invoice $invoice_number total: $total_amount");

        return $this->success('Sales invoice created successfully.', array_merge(['id' => $sale_id], $sales_data), 201);
    }

    /**
     * DELETE /sales/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->salesRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        // Revert outstanding balance
        $customer = $this->customerRepository->findById($invoice['customer_id']);
        if ($customer) {
            $new_outstanding = max(0.00, floatval($customer['outstanding_amount']) - floatval($invoice['total_amount']));
            $this->customerRepository->update($invoice['customer_id'], ['outstanding_amount' => $new_outstanding], ['%f']);
        }

        // Revert items stock
        $items = $this->salesRepository->getInvoiceItems($id);
        foreach ($items as $item) {
            $item_record = $this->itemRepository->findById($item['item_id']);
            if ($item_record && $item_record['item_type'] === 'Product') {
                $new_stock = intval($item_record['stock_quantity']) + intval($item['quantity']);
                $this->itemRepository->update($item['item_id'], ['stock_quantity' => $new_stock], ['%d']);
            }
        }

        $deleted = $this->salesRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete sales invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'SALES_INVOICE_DELETE', "Soft deleted invoice ID: $id ($invoice[invoice_number])");

        return $this->success('Invoice deleted successfully.');
    }
}
