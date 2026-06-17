<?php
namespace ServiceManagementApi\Controllers;

use ServiceManagementApi\Repositories\PaymentRepository;
use ServiceManagementApi\Repositories\InvoiceRepository;
use ServiceManagementApi\Services\AuthService;
use WP_REST_Request;

class PaymentController extends BaseController {
    private $paymentRepository;
    private $invoiceRepository;

    public function __construct() {
        $this->paymentRepository = new PaymentRepository();
        $this->invoiceRepository = new InvoiceRepository();
    }

    /**
     * GET /payments
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'payment_number', 'payment_date', 'amount', 'payment_method'];
        $search_fields = ['payment_number', 'payment_method', 'transaction_reference', 'remarks'];

        $extra_filters = [];
        if (isset($params['invoice_id'])) {
            $extra_filters['invoice_id'] = intval($params['invoice_id']);
        }

        $results = $this->paymentRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

        foreach ($results['data'] as &$row) {
            $invoice = $this->invoiceRepository->findById($row['invoice_id']);
            $row['invoice_number'] = $invoice ? $invoice['invoice_number'] : '';
            $row['customer_name'] = $invoice ? $invoice['customer_name'] : 'Unknown';
        }

        return $this->success('Payments list retrieved.', $results);
    }

    /**
     * GET /payments/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $payment = $this->paymentRepository->findById($id);

        if (!$payment) {
            return $this->error('Payment details not found.', [], 404);
        }

        $invoice = $this->invoiceRepository->findById($payment['invoice_id']);
        $payment['invoice_number'] = $invoice ? $invoice['invoice_number'] : '';
        $payment['customer_name'] = $invoice ? $invoice['customer_name'] : 'Unknown';

        return $this->success('Payment details retrieved.', $payment);
    }

    /**
     * POST /payments
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['invoice_id']) || empty($params['amount']) || empty($params['payment_date'])) {
            return $this->error('invoice_id, amount, and payment_date are required.');
        }

        $invoice_id = intval($params['invoice_id']);
        $amount = floatval($params['amount']);
        $invoice = $this->invoiceRepository->findById($invoice_id);

        if (!$invoice) {
            return $this->error('Invoice record not found.');
        }

        // Generate payment number
        $payment_number = 'PAY-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        while ($this->paymentRepository->existsPaymentNumber($payment_number)) {
            $payment_number = 'PAY-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        }

        $data = [
            'payment_number' => $payment_number,
            'invoice_id' => $invoice_id,
            'amount' => $amount,
            'payment_date' => sanitize_text_field($params['payment_date']),
            'payment_method' => sanitize_text_field($params['payment_method'] ?? 'Cash'),
            'transaction_reference' => sanitize_text_field($params['transaction_reference'] ?? ''),
            'remarks' => sanitize_textarea_field($params['remarks'] ?? '')
        ];

        $formats = ['%s', '%d', '%f', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->paymentRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to log payment transaction.');
        }

        // Calculate total payments received for this invoice
        global $wpdb;
        $table_payments = $wpdb->prefix . 'ser_payments';
        $total_paid = (float)$wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM $table_payments WHERE invoice_id = %d", $invoice_id)) ?: 0.00;
        
        $invoice_total = (float)$invoice['total_amount'];
        $new_status = 'Unpaid';
        if ($total_paid >= $invoice_total) {
            $new_status = 'Paid';
        } elseif ($total_paid > 0) {
            $new_status = 'Partially Paid';
        }
        
        $this->invoiceRepository->update($invoice_id, ['status' => $new_status], ['%s']);

        AuthService::logActivity(
            get_current_user_id(),
            'PAYMENT_CREATE',
            "Recorded payment $payment_number of amount ₹$amount against invoice $invoice[invoice_number]"
        );

        return $this->success('Payment recorded successfully.', ['id' => $inserted_id, 'payment_number' => $payment_number], 201);
    }

    /**
     * DELETE /payments/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $payment = $this->paymentRepository->findById($id);

        if (!$payment) {
            return $this->error('Payment details not found.', [], 404);
        }

        $invoice_id = $payment['invoice_id'];
        $deleted = $this->paymentRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete payment transaction.');
        }

        // Recalculate invoice status
        global $wpdb;
        $table_payments = $wpdb->prefix . 'ser_payments';
        $total_paid = (float)$wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM $table_payments WHERE invoice_id = %d", $invoice_id)) ?: 0.00;
        
        $invoice = $this->invoiceRepository->findById($invoice_id);
        if ($invoice) {
            $invoice_total = (float)$invoice['total_amount'];
            $new_status = 'Unpaid';
            if ($total_paid >= $invoice_total) {
                $new_status = 'Paid';
            } elseif ($total_paid > 0) {
                $new_status = 'Partially Paid';
            }
            $this->invoiceRepository->update($invoice_id, ['status' => $new_status], ['%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'PAYMENT_DELETE', "Deleted payment transaction $id");

        return $this->success('Payment deleted successfully.');
    }
}
