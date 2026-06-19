<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\InvoiceRepository;
use WorkspaceErpApi\Repositories\PaymentRepository;
use WorkspaceErpApi\Services\AuthService;
use WP_REST_Request;

class BillingController extends BaseController {
    private $invoiceRepo;
    private $paymentRepo;

    public function __construct() {
        $this->invoiceRepo = new InvoiceRepository();
        $this->paymentRepo = new PaymentRepository();
    }

    public function indexInvoices(WP_REST_Request $request) {
        return $this->success('Invoices fetched successfully', $this->invoiceRepo->findAll($request->get_params(), ['id', 'invoice_no', 'status'], ['invoice_no']));
    }

    public function createInvoice(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['client_id']) || empty($params['base_amount'])) {
            return $this->error('client_id and base_amount are required.');
        }

        $no = 'INV-' . date('Y') . '-' . rand(1000, 9999);
        $base = floatval($params['base_amount']);
        $gst = round($base * 0.18, 2);
        $total = $base + $gst;

        $data = [
            'invoice_no' => $no,
            'client_id' => intval($params['client_id']),
            'billing_type' => isset($params['billing_type']) ? sanitize_text_field($params['billing_type']) : 'LEASE',
            'billing_month' => isset($params['billing_month']) ? sanitize_text_field($params['billing_month']) : date('Y-m'),
            'base_amount' => $base,
            'gst_percentage' => 18.00,
            'gst_amount' => $gst,
            'total_amount' => $total,
            'due_date' => isset($params['due_date']) ? sanitize_text_field($params['due_date']) : date('Y-m-d', strtotime('+10 days')),
            'status' => 'PENDING',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->invoiceRepo->create($data, ['%s', '%d', '%s', '%s', '%f', '%f', '%f', '%f', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Invoice generated successfully', array_merge(['id' => $id], $data), 201);
    }

    public function recordPayment(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->invoiceRepo->findById($id);
        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        if ($invoice['status'] === 'PAID') {
            return $this->error('Invoice is already paid.', [], 400);
        }

        $params = $request->get_json_params();
        $amount = isset($params['amount']) ? floatval($params['amount']) : floatval($invoice['total_amount']);
        $payment_method = isset($params['payment_method']) ? sanitize_text_field($params['payment_method']) : 'CASH';
        $transaction_id = isset($params['transaction_id']) ? sanitize_text_field($params['transaction_id']) : ('TXN-MAN-' . strtoupper(substr(md5(time() . rand()), 0, 10)));
        $payment_date = isset($params['payment_date']) ? sanitize_text_field($params['payment_date']) : current_time('mysql');

        // Update invoice status to PAID
        $invoice_updated = $this->invoiceRepo->update($id, [
            'status' => 'PAID',
            'updated_at' => current_time('mysql')
        ], ['%s', '%s']);

        if (!$invoice_updated) {
            return $this->error('Failed to update invoice status.');
        }

        // Create payment record
        $payment_data = [
            'invoice_id' => $id,
            'client_id' => intval($invoice['client_id']),
            'amount' => $amount,
            'payment_date' => $payment_date,
            'payment_method' => $payment_method,
            'transaction_id' => $transaction_id,
            'gateway' => 'OFFLINE_MANUAL',
            'status' => 'COMPLETED',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $payment_id = $this->paymentRepo->create($payment_data, ['%d', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        
        if (!$payment_id) {
            return $this->error('Failed to record payment transaction.');
        }

        AuthService::logActivity(get_current_user_id(), 'RECORD_PAYMENT', "Recorded payment of ₹$amount for Invoice " . $invoice['invoice_no'] . " (ID: $id)");

        return $this->success('Payment recorded successfully', array_merge(['id' => $payment_id], $payment_data));
    }

    public function updateInvoice(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $invoice = $this->invoiceRepo->findById($id);
        if (!$invoice) return $this->error('Invoice not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['client_id'])) { $update['client_id'] = intval($params['client_id']); $formats[] = '%d'; }
        if (isset($params['billing_type'])) { $update['billing_type'] = sanitize_text_field($params['billing_type']); $formats[] = '%s'; }
        if (isset($params['billing_month'])) { $update['billing_month'] = sanitize_text_field($params['billing_month']); $formats[] = '%s'; }
        if (isset($params['base_amount'])) { 
            $update['base_amount'] = floatval($params['base_amount']); 
            $formats[] = '%f'; 
            $gst_pct = isset($params['gst_percentage']) ? floatval($params['gst_percentage']) : floatval($invoice['gst_percentage']);
            $update['gst_amount'] = round($update['base_amount'] * ($gst_pct / 100), 2);
            $formats[] = '%f';
            $update['total_amount'] = $update['base_amount'] + $update['gst_amount'];
            $formats[] = '%f';
        }
        if (isset($params['gst_percentage']) && !isset($update['base_amount'])) {
            $update['gst_percentage'] = floatval($params['gst_percentage']);
            $formats[] = '%f';
            $update['gst_amount'] = round(floatval($invoice['base_amount']) * ($update['gst_percentage'] / 100), 2);
            $formats[] = '%f';
            $update['total_amount'] = floatval($invoice['base_amount']) + $update['gst_amount'];
            $formats[] = '%f';
        }
        if (isset($params['due_date'])) { $update['due_date'] = sanitize_text_field($params['due_date']); $formats[] = '%s'; }
        if (isset($params['notes'])) { $update['notes'] = sanitize_textarea_field($params['notes']); $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->invoiceRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update invoice.');

        AuthService::logActivity(get_current_user_id(), 'UPDATE_INVOICE', "Updated invoice details ID: $id");
        return $this->success('Invoice updated successfully', $this->invoiceRepo->findById($id));
    }

    public function deleteInvoice(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $invoice = $this->invoiceRepo->findById($id);
        if (!$invoice) return $this->error('Invoice not found.', [], 404);

        $this->invoiceRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_INVOICE', "Soft deleted invoice ID: $id");
        return $this->success('Invoice deleted successfully');
    }

    public function indexPayments(WP_REST_Request $request) {
        return $this->success('Payments fetched successfully', $this->paymentRepo->findAll($request->get_params(), ['id', 'amount', 'status'], []));
    }
}
