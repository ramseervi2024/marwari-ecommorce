<?php
namespace ServiceManagementApi\Controllers;

use ServiceManagementApi\Repositories\InvoiceRepository;
use ServiceManagementApi\Repositories\JobRepository;
use ServiceManagementApi\Repositories\AmcRepository;
use ServiceManagementApi\Services\AuthService;
use WP_REST_Request;

class InvoiceController extends BaseController {
    private $invoiceRepository;
    private $jobRepository;
    private $amcRepository;

    public function __construct() {
        $this->invoiceRepository = new InvoiceRepository();
        $this->jobRepository = new JobRepository();
        $this->amcRepository = new AmcRepository();
    }

    /**
     * GET /invoices
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'invoice_number', 'invoice_date', 'total_amount', 'status'];
        $search_fields = ['invoice_number', 'customer_name', 'email', 'phone', 'status'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->invoiceRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

        foreach ($results['data'] as &$row) {
            if (!empty($row['job_id'])) {
                $job = $this->jobRepository->findById($row['job_id']);
                $row['job_number'] = $job ? $job['job_number'] : '';
            } else {
                $row['job_number'] = '';
            }

            if (!empty($row['amc_id'])) {
                $amc = $this->amcRepository->findById($row['amc_id']);
                $row['contract_number'] = $amc ? $amc['contract_number'] : '';
            } else {
                $row['contract_number'] = '';
            }
        }

        return $this->success('Invoices list retrieved.', $results);
    }

    /**
     * GET /invoices/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->invoiceRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        if (!empty($invoice['job_id'])) {
            $job = $this->jobRepository->findById($invoice['job_id']);
            $invoice['job_number'] = $job ? $job['job_number'] : '';
        } else {
            $invoice['job_number'] = '';
        }

        if (!empty($invoice['amc_id'])) {
            $amc = $this->amcRepository->findById($invoice['amc_id']);
            $invoice['contract_number'] = $amc ? $amc['contract_number'] : '';
        } else {
            $invoice['contract_number'] = '';
        }

        return $this->success('Invoice details retrieved successfully.', $invoice);
    }

    /**
     * POST /invoices
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['customer_name']) || empty($params['total_amount'])) {
            return $this->error('customer_name and total_amount are required.');
        }

        // Generate invoice number
        $invoice_number = 'INV-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        while ($this->invoiceRepository->existsInvoiceNumber($invoice_number)) {
            $invoice_number = 'INV-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        }

        $invoice_date = sanitize_text_field($params['invoice_date'] ?? date('Y-m-d'));
        $job_id = isset($params['job_id']) ? intval($params['job_id']) : null;
        $amc_id = isset($params['amc_id']) ? intval($params['amc_id']) : null;

        $data = [
            'invoice_number' => $invoice_number,
            'job_id' => $job_id,
            'amc_id' => $amc_id,
            'customer_name' => sanitize_text_field($params['customer_name']),
            'email' => sanitize_email($params['email'] ?? ''),
            'phone' => sanitize_text_field($params['phone'] ?? ''),
            'invoice_date' => $invoice_date,
            'total_amount' => floatval($params['total_amount']),
            'status' => sanitize_text_field($params['status'] ?? 'Unpaid')
        ];

        $formats = ['%s', '%d', '%d', '%s', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->invoiceRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to generate invoice.');
        }

        // Update Job state if job invoice is generated
        if ($job_id) {
            $this->jobRepository->update($job_id, ['status' => 'Completed'], ['%s']);
        }

        AuthService::logActivity(
            get_current_user_id(),
            'INVOICE_CREATE',
            "Generated invoice $invoice_number (Amount: {$data['total_amount']}) for {$data['customer_name']}"
        );

        return $this->success('Invoice generated successfully.', ['id' => $inserted_id, 'invoice_number' => $invoice_number], 201);
    }

    /**
     * PUT /invoices/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->invoiceRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'customer_name' => '%s',
            'email' => '%s',
            'phone' => '%s',
            'invoice_date' => '%s',
            'total_amount' => '%f',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                } elseif ($field === 'total_amount') {
                    $data[$field] = floatval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->invoiceRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update invoice details.');
        }

        AuthService::logActivity(get_current_user_id(), 'INVOICE_UPDATE', "Updated invoice details/status of ID: $id");

        return $this->success('Invoice updated successfully.', $this->invoiceRepository->findById($id));
    }

    /**
     * DELETE /invoices/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $invoice = $this->invoiceRepository->findById($id);

        if (!$invoice) {
            return $this->error('Invoice not found.', [], 404);
        }

        $deleted = $this->invoiceRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'INVOICE_DELETE', "Soft deleted invoice ID: $id ({$invoice['invoice_number']})");

        return $this->success('Invoice deleted successfully.');
    }
}
