<?php
namespace ConstructionManagementApi\Controllers;

use ConstructionManagementApi\Repositories\BillingRepository;
use ConstructionManagementApi\Services\AuthService;
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
        $allowed_sorts = ['id', 'invoice_number', 'project_id', 'client_name', 'invoice_amount', 'payment_status', 'invoice_date'];
        $search_fields = ['invoice_number', 'client_name', 'milestone_name', 'payment_status'];

        $extra_filters = [];
        if (isset($params['project_id'])) {
            $extra_filters['project_id'] = intval($params['project_id']);
        }
        if (isset($params['payment_status'])) {
            $extra_filters['payment_status'] = sanitize_text_field($params['payment_status']);
        }

        $results = $this->billingRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Billing invoices list retrieved successfully.', $results);
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

        if (empty($params['project_id']) || empty($params['client_name']) || empty($params['invoice_amount'])) {
            return $this->error('Validation failed: project_id, client_name, and invoice_amount are required.');
        }

        $invoice_number = 'INV-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        while ($this->billingRepository->existsInvoiceNumber($invoice_number)) {
            $invoice_number = 'INV-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        }

        $amount = floatval($params['invoice_amount']);
        $gst_amount = $amount * 0.18; // default 18% GST

        $data = [
            'invoice_number' => $invoice_number,
            'project_id' => intval($params['project_id']),
            'client_name' => sanitize_text_field($params['client_name']),
            'milestone_name' => sanitize_text_field($params['milestone_name'] ?? ''),
            'invoice_amount' => $amount,
            'gst_amount' => $gst_amount,
            'payment_status' => sanitize_text_field($params['payment_status'] ?? 'PENDING'),
            'invoice_date' => !empty($params['invoice_date']) ? sanitize_text_field($params['invoice_date']) : current_time('Y-m-d')
        ];

        $formats = ['%s', '%d', '%s', '%s', '%f', '%f', '%s', '%s'];
        $inserted_id = $this->billingRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create billing invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILLING_CREATE', "Created billing invoice $invoice_number ($inserted_id) amount: $amount");

        return $this->success('Invoice created successfully.', array_merge(['id' => $inserted_id], $data), 201);
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
        
        $fields = ['project_id', 'client_name', 'milestone_name', 'invoice_amount', 'payment_status', 'invoice_date'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'project_id') {
                    $data[$field] = intval($params[$field]);
                    $formats[] = '%d';
                } elseif ($field === 'invoice_amount') {
                    $amount = floatval($params[$field]);
                    $data[$field] = $amount;
                    $data['gst_amount'] = $amount * 0.18;
                    $formats[] = '%f';
                    $formats[] = '%f';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->billingRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILLING_UPDATE', "Updated invoice record ID: $id");

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
}
