<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\EwaybillRepository;
use AccountingManagementApi\Repositories\SalesRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class EwaybillController extends BaseController {
    private $ewaybillRepository;
    private $salesRepository;

    public function __construct() {
        $this->ewaybillRepository = new EwaybillRepository();
        $this->salesRepository = new SalesRepository();
    }

    /**
     * GET /ewaybill
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'invoice_id', 'eway_bill_number', 'created_at'];
        $search_fields = ['eway_bill_number', 'vehicle_number', 'transporter_name', 'status'];
        
        $extra_filters = [];
        if (isset($params['invoice_id'])) {
            $extra_filters['invoice_id'] = intval($params['invoice_id']);
        }

        $results = $this->ewaybillRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('E-Way bills list retrieved.', $results);
    }

    /**
     * POST /ewaybill/generate
     */
    public function generate(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['invoice_id']) || empty($params['vehicle_number']) || empty($params['transporter_name'])) {
            return $this->error('invoice_id, vehicle_number, and transporter_name are required.');
        }

        $invoice_id = intval($params['invoice_id']);
        $invoice = $this->salesRepository->findById($invoice_id);
        if (!$invoice) {
            return $this->error('Sales invoice not found.');
        }

        $existing = $this->ewaybillRepository->findByInvoiceId($invoice_id);
        if ($existing) {
            return $this->success('E-Way bill already exists.', $existing);
        }

        // Generate mock e-way bill number
        $eway_bill_num = 'EWB' . date('Ymd') . sprintf('%06d', rand(100000, 999999));
        
        $data = [
            'invoice_id' => $invoice_id,
            'eway_bill_number' => $eway_bill_num,
            'vehicle_number' => sanitize_text_field($params['vehicle_number']),
            'transporter_name' => sanitize_text_field($params['transporter_name']),
            'distance' => intval($params['distance'] ?? 120),
            'status' => 'ACTIVE'
        ];

        $formats = ['%d', '%s', '%s', '%s', '%d', '%s'];
        $inserted_id = $this->ewaybillRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create E-Way bill.');
        }

        AuthService::logActivity(get_current_user_id(), 'EWAYBILL_CREATE', "Generated E-Way Bill: $eway_bill_num");

        return $this->success('E-Way bill generated successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }
}
