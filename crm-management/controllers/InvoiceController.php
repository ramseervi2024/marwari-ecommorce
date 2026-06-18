<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\InvoiceRepository;
use CrmManagementApi\Repositories\PaymentRepository;
use CrmManagementApi\Repositories\CustomerRepository;
use CrmManagementApi\Repositories\DealRepository;
use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

class InvoiceController extends BaseController {
    private $invoiceRepository;
    private $paymentRepository;
    private $customerRepository;
    private $dealRepository;

    public function __construct() {
        $this->invoiceRepository  = new InvoiceRepository();
        $this->paymentRepository  = new PaymentRepository();
        $this->customerRepository = new CustomerRepository();
        $this->dealRepository     = new DealRepository();
    }

    /**
     * GET /invoices
     */
    public function getInvoices(WP_REST_Request $request) {
        $params = $request->get_params();
        $current_user = wp_get_current_user();

        $allowed_sorts = ['id', 'invoice_number', 'invoice_date', 'due_date', 'grand_total', 'status'];
        $extra_filters = [];

        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        // Customer restriction: see only their own invoices
        if (in_array('crm_customer', (array)$current_user->roles)) {
            $cust = $this->customerRepository->findByUserId($current_user->ID);
            if (!$cust) {
                return $this->success('Invoices list (empty).', [
                    'total' => 0, 'page' => 1, 'limit' => 10, 'pages' => 0, 'data' => []
                ]);
            }
            $extra_filters['customer_id'] = $cust['id'];
        } elseif (isset($params['customer_id'])) {
            $extra_filters['customer_id'] = intval($params['customer_id']);
        }

        $results = $this->invoiceRepository->findAll($params, $allowed_sorts, [], $extra_filters);

        // Map names
        foreach ($results['data'] as &$row) {
            $cust = $this->customerRepository->findById($row['customer_id']);
            $row['company_name']   = $cust ? $cust['company_name'] : 'Unknown Customer';
            $row['contact_person'] = $cust ? $cust['contact_person'] : '';
            
            if ($row['deal_id']) {
                $deal = $this->dealRepository->findById($row['deal_id']);
                $row['deal_number'] = $deal ? $deal['deal_number'] : '';
            } else {
                $row['deal_number'] = '';
            }

            $row['items'] = json_decode($row['items'] ?? '[]', true);
        }

        return $this->success('Invoices list retrieved.', $results);
    }

    /**
     * POST /invoices
     */
    public function createInvoice(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['customer_id']) || empty($params['invoice_date']) || empty($params['due_date'])) {
            return $this->error('customer_id, invoice_date, and due_date are required.');
        }

        $cust_id = intval($params['customer_id']);
        $cust = $this->customerRepository->findById($cust_id);
        if (!$cust) {
            return $this->error('Customer not found.', [], 404);
        }

        // Auto generate invoice number
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_invoices';
        $max_id = (int)$wpdb->get_var("SELECT MAX(id) FROM $table_name") + 1;
        $invoice_number = 'INV-' . date('Y') . '-' . sprintf('%04d', $max_id);

        $subtotal   = floatval($params['subtotal'] ?? 0);
        $tax_amount = floatval($params['tax_amount'] ?? 0);
        $grand_total = $subtotal + $tax_amount;
        $items = isset($params['items']) ? json_encode($params['items']) : '[]';

        $data = [
            'invoice_number' => $invoice_number,
            'deal_id'        => !empty($params['deal_id']) ? intval($params['deal_id']) : null,
            'customer_id'    => $cust_id,
            'invoice_date'   => sanitize_text_field($params['invoice_date']),
            'due_date'       => sanitize_text_field($params['due_date']),
            'subtotal'       => $subtotal,
            'tax_amount'     => $tax_amount,
            'grand_total'    => $grand_total,
            'status'         => sanitize_text_field($params['status'] ?? 'Unpaid'),
            'items'          => $items
        ];

        $formats = ['%s', '%d', '%d', '%s', '%s', '%f', '%f', '%f', '%s', '%s'];

        $id = $this->invoiceRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to create invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'INVOICE_CREATE', "Created invoice: $invoice_number");

        return $this->success('Invoice created successfully.', $this->invoiceRepository->findById($id), 201);
    }

    /**
     * GET /invoices/{id}
     */
    public function getInvoice(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->invoiceRepository->findById($id);
        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }
        $current_user = wp_get_current_user();
        if (in_array('crm_customer', (array)$current_user->roles)) {
            $cust = $this->customerRepository->findByUserId($current_user->ID);
            if (!$cust || intval($invoice['customer_id']) !== intval($cust['id'])) {
                return $this->error('Access Denied.', [], 403);
            }
        }
        $invoice['items'] = json_decode($invoice['items'] ?? '[]', true);
        $cust = $this->customerRepository->findById($invoice['customer_id']);
        $invoice['company_name'] = $cust ? $cust['company_name'] : '';
        return $this->success('Invoice retrieved.', $invoice);
    }

    /**
     * DELETE /invoices/{id}
     */
    public function deleteInvoice(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->invoiceRepository->findById($id);
        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }
        if (!current_user_can('manage_crm_settings') && !current_user_can('view_crm_reports')) {
            return $this->error('Access Denied.', [], 403);
        }
        $this->invoiceRepository->delete($id);
        AuthService::logActivity(get_current_user_id(), 'INVOICE_DELETE', "Deleted invoice ID: $id");
        return $this->success('Invoice deleted successfully.');
    }

    /**
     * PUT /invoices/{id}
     */
    public function updateInvoice(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->invoiceRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'invoice_date' => '%s',
            'due_date'     => '%s',
            'subtotal'     => '%f',
            'tax_amount'   => '%f',
            'status'       => '%s'
        ];

        foreach ($fields as $key => $fmt) {
            if (isset($params[$key])) {
                $data[$key] = ($fmt === '%f') ? floatval($params[$key]) : sanitize_text_field($params[$key]);
                $formats[] = $fmt;
            }
        }

        if (isset($params['items'])) {
            $data['items'] = json_encode($params['items']);
            $formats[] = '%s';
        }

        if (isset($data['subtotal']) || isset($data['tax_amount'])) {
            $sub = isset($data['subtotal']) ? $data['subtotal'] : floatval($invoice['subtotal']);
            $tax = isset($data['tax_amount']) ? $data['tax_amount'] : floatval($invoice['tax_amount']);
            $data['grand_total'] = $sub + $tax;
            $formats[] = '%f';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->invoiceRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'INVOICE_UPDATE', "Updated invoice ID: $id ($invoice[invoice_number])");

        return $this->success('Invoice updated successfully.', $this->invoiceRepository->findById($id));
    }

    /**
     * GET /payments
     */
    public function getPayments(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'invoice_id', 'payment_date', 'amount', 'payment_mode'];
        $extra_filters = [];

        if (isset($params['invoice_id'])) {
            $extra_filters['invoice_id'] = intval($params['invoice_id']);
        }

        $results = $this->paymentRepository->findAll($params, $allowed_sorts, [], $extra_filters);

        foreach ($results['data'] as &$row) {
            $inv = $this->invoiceRepository->findById($row['invoice_id']);
            $row['invoice_number'] = $inv ? $inv['invoice_number'] : '';
        }

        return $this->success('Payments list retrieved.', $results);
    }

    /**
     * POST /payments
     */
    public function createPayment(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['invoice_id']) || empty($params['amount']) || empty($params['payment_date'])) {
            return $this->error('invoice_id, amount, and payment_date are required.');
        }

        $invoice_id = intval($params['invoice_id']);
        $invoice = $this->invoiceRepository->findById($invoice_id);
        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        $amount = floatval($params['amount']);
        $data = [
            'invoice_id'            => $invoice_id,
            'payment_date'          => sanitize_text_field($params['payment_date']),
            'amount'                => $amount,
            'payment_mode'          => sanitize_text_field($params['payment_mode'] ?? 'Bank Transfer'),
            'transaction_reference' => sanitize_text_field($params['transaction_reference'] ?? ''),
            'status'                => 'Success'
        ];

        $formats = ['%d', '%s', '%f', '%s', '%s', '%s'];

        $id = $this->paymentRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to log payment.');
        }

        // Auto reconcile invoice status: mark Paid or Partially Paid
        // Sum all success payments for this invoice
        global $wpdb;
        $table_payments = $wpdb->prefix . 'crm_payments';
        $total_paid = (float)$wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM $table_payments WHERE invoice_id = %d AND status = 'Success'", $invoice_id));

        $grand_total = floatval($invoice['grand_total']);
        $new_status = 'Unpaid';
        if ($total_paid >= $grand_total) {
            $new_status = 'Paid';
        } elseif ($total_paid > 0) {
            $new_status = 'Partial';
        }

        $this->invoiceRepository->update($invoice_id, ['status' => $new_status], ['%s']);

        AuthService::logActivity(get_current_user_id(), 'PAYMENT_CREATE', "Logged payment of $amount for Invoice ID: $invoice_id (Invoice Status: $new_status)");

        return $this->success('Payment recorded successfully.', $this->paymentRepository->findById($id), 201);
    }

    /**
     * PUT /payments/{id}
     */
    public function updatePayment(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $payment = $this->paymentRepository->findById($id);
        if (!$payment) {
            return $this->error('Payment not found.', [], 404);
        }
        $params = $request->get_json_params();
        $data = []; $formats = [];
        if (isset($params['payment_date']))          { $data['payment_date']          = sanitize_text_field($params['payment_date']);          $formats[] = '%s'; }
        if (isset($params['amount']))                { $data['amount']                = floatval($params['amount']);                           $formats[] = '%f'; }
        if (isset($params['payment_mode']))          { $data['payment_mode']          = sanitize_text_field($params['payment_mode']);          $formats[] = '%s'; }
        if (isset($params['transaction_reference'])) { $data['transaction_reference'] = sanitize_text_field($params['transaction_reference']); $formats[] = '%s'; }
        if (isset($params['status']))                { $data['status']                = sanitize_text_field($params['status']);                $formats[] = '%s'; }
        if (empty($data)) { return $this->error('No parameters to update.'); }
        $this->paymentRepository->update($id, $data, $formats);
        AuthService::logActivity(get_current_user_id(), 'PAYMENT_UPDATE', "Updated payment ID: $id");
        return $this->success('Payment updated.', $this->paymentRepository->findById($id));
    }

    /**
     * DELETE /payments/{id}
     */
    public function deletePayment(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $payment = $this->paymentRepository->findById($id);
        if (!$payment) {
            return $this->error('Payment not found.', [], 404);
        }
        if (!current_user_can('manage_crm_settings')) {
            return $this->error('Access Denied.', [], 403);
        }
        $this->paymentRepository->delete($id);
        AuthService::logActivity(get_current_user_id(), 'PAYMENT_DELETE', "Deleted payment ID: $id");
        return $this->success('Payment deleted.');
    }
}
