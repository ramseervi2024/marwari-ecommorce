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

    public function indexPayments(WP_REST_Request $request) {
        return $this->success('Payments fetched successfully', $this->paymentRepo->findAll($request->get_params(), ['id', 'amount', 'status'], []));
    }
}
