<?php
namespace TransportManagementApi\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Repositories\BillingRepository;
use TransportManagementApi\Services\AuthService;
use WP_REST_Request;

class BillingController extends BaseController {
    private $billingRepository;

    public function __construct() {
        $this->billingRepository = new BillingRepository();
    }

    /**
     * GET /billing
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'invoice_number', 'total_amount', 'invoice_date', 'payment_status', 'created_at'];
        $search_fields = ['invoice_number'];
        
        $extra_filters = [];
        if (isset($params['payment_status'])) {
            $extra_filters['payment_status'] = sanitize_text_field($params['payment_status']);
        }
        if (isset($params['customer_id'])) {
            $extra_filters['customer_id'] = intval($params['customer_id']);
        }
        if (isset($params['trip_id'])) {
            $extra_filters['trip_id'] = intval($params['trip_id']);
        }

        $results = $this->billingRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Billing invoices retrieved successfully.', $results);
    }

    /**
     * GET /billing/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->billingRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        return $this->success('Invoice retrieved successfully.', $invoice);
    }

    /**
     * POST /billing
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['trip_id']) || empty($params['customer_id']) || empty($params['freight_amount'])) {
            return $this->error('Validation failed: trip_id, customer_id, and freight_amount are required.');
        }

        // Generate custom invoice number
        $invoice_number = 'INV-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        while ($this->billingRepository->existsInvoiceNumber($invoice_number)) {
            $invoice_number = 'INV-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        }

        $freight = floatval($params['freight_amount']);
        $surcharge = floatval($params['fuel_surcharge'] ?? 0.00);
        $taxable = $freight + $surcharge;
        
        // 18% GST standard on logistics
        $gst = $taxable * 0.18;
        $total = $taxable + $gst;

        $data = [
            'invoice_number' => $invoice_number,
            'trip_id' => intval($params['trip_id']),
            'customer_id' => intval($params['customer_id']),
            'freight_amount' => $freight,
            'fuel_surcharge' => $surcharge,
            'gst_amount' => $gst,
            'total_amount' => $total,
            'payment_status' => sanitize_text_field($params['payment_status'] ?? 'Unpaid'),
            'invoice_date' => !empty($params['invoice_date']) ? sanitize_text_field($params['invoice_date']) : date('Y-m-d')
        ];

        $formats = ['%s', '%d', '%d', '%f', '%f', '%f', '%f', '%s', '%s'];
        $inserted_id = $this->billingRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to generate invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILLING_CREATE', "Generated invoice $invoice_number ($inserted_id) for customer ID {$data['customer_id']} value ₹{$total}");

        return $this->success('Invoice generated successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /billing/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->billingRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'trip_id' => '%d',
            'customer_id' => '%d',
            'freight_amount' => '%f',
            'fuel_surcharge' => '%f',
            'payment_status' => '%s',
            'invoice_date' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $data[$field] = intval($params[$field]);
                } elseif ($format === '%f') {
                    $data[$field] = floatval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        // Recalculate invoice totals if freight or surcharge changed
        if (isset($data['freight_amount']) || isset($data['fuel_surcharge'])) {
            $freight = isset($data['freight_amount']) ? $data['freight_amount'] : floatval($invoice['freight_amount']);
            $surcharge = isset($data['fuel_surcharge']) ? $data['fuel_surcharge'] : floatval($invoice['fuel_surcharge']);
            $taxable = $freight + $surcharge;
            $data['gst_amount'] = $taxable * 0.18;
            $data['total_amount'] = $taxable + $data['gst_amount'];
            $formats[] = '%f';
            $formats[] = '%f';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->billingRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILLING_UPDATE', "Updated invoice ID: $id status: " . ($data['payment_status'] ?? 'N/A'));

        return $this->success('Invoice updated successfully.', $this->billingRepository->findById($id));
    }

    /**
     * DELETE /billing/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->billingRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        $deleted = $this->billingRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILLING_DELETE', "Soft deleted invoice ID: $id ($invoice[invoice_number])");

        return $this->success('Invoice deleted successfully.');
    }

    /**
     * GET /billing/:id/pdf
     * Mock HTML PDF receipt generation
     */
    public function getInvoicePdf(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->billingRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        // Return a structured PDF metadata mock details
        $pdf_html = "
        <div style='font-family: monospace; padding: 20px; color: #333;'>
            <h2>TAX INVOICE - TRANSPORT & LOGISTICS</h2>
            <hr/>
            <p><strong>Invoice Number:</strong> {$invoice['invoice_number']}</p>
            <p><strong>Date:</strong> {$invoice['invoice_date']}</p>
            <p><strong>Payment Status:</strong> {$invoice['payment_status']}</p>
            <hr/>
            <table style='width: 100%; text-align: left;'>
                <tr><th>Description</th><th>Amount (INR)</th></tr>
                <tr><td>Basic Freight Charges</td><td>" . number_format($invoice['freight_amount'], 2) . "</td></tr>
                <tr><td>Fuel Surcharge</td><td>" . number_format($invoice['fuel_surcharge'], 2) . "</td></tr>
                <tr><td>GST (18%)</td><td>" . number_format($invoice['gst_amount'], 2) . "</td></tr>
                <tr style='font-weight: bold;'><td>Total Due</td><td>" . number_format($invoice['total_amount'], 2) . "</td></tr>
            </table>
            <hr/>
            <p>Generated via Transport ERP Client Portal.</p>
        </div>";

        return $this->success('Invoice PDF structure retrieved successfully.', [
            'invoice_number' => $invoice['invoice_number'],
            'html_preview' => $pdf_html,
            'download_url' => site_url("/wp-json/transport-management/v1/billing/{$id}/pdf?download=1")
        ]);
    }
}
