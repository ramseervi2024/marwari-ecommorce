<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\EinvoiceRepository;
use AccountingManagementApi\Repositories\SalesRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class EinvoiceController extends BaseController {
    private $einvoiceRepository;
    private $salesRepository;

    public function __construct() {
        $this->einvoiceRepository = new EinvoiceRepository();
        $this->salesRepository = new SalesRepository();
    }

    /**
     * GET /einvoice
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'invoice_id', 'created_at'];
        $search_fields = ['irn_number', 'ack_number', 'status'];
        
        $extra_filters = [];
        if (isset($params['invoice_id'])) {
            $extra_filters['invoice_id'] = intval($params['invoice_id']);
        }

        $results = $this->einvoiceRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('E-Invoice logs retrieved.', $results);
    }

    /**
     * POST /einvoice/generate
     */
    public function generate(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['invoice_id'])) {
            return $this->error('invoice_id is required.');
        }

        $invoice_id = intval($params['invoice_id']);
        $invoice = $this->salesRepository->findById($invoice_id);
        if (!$invoice) {
            return $this->error('Sales invoice not found.');
        }

        $existing = $this->einvoiceRepository->findByInvoiceId($invoice_id);
        if ($existing) {
            return $this->success('E-Invoice already generated.', $existing);
        }

        // Generate mock IRN, Ack, and QR code
        $irn = hash('sha256', $invoice['invoice_number'] . time());
        $ack = strval(rand(1000000000, 9999999999));
        
        $data = [
            'invoice_id' => $invoice_id,
            'irn_number' => $irn,
            'ack_number' => $ack,
            'ack_date' => current_time('mysql'),
            'qr_code' => "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($irn),
            'status' => 'ACTIVE'
        ];

        $formats = ['%d', '%s', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->einvoiceRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to register e-invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'EINVOICE_GENERATE', "Generated IRN for Sales Invoice ID: $invoice_id");

        return $this->success('E-Invoice generated successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }
}
