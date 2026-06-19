<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\VendorRepository;
use WorkspaceErpApi\Repositories\VendorPaymentRepository;
use WP_REST_Request;

class VendorController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new VendorRepository();
    }

    public function index(WP_REST_Request $request) {
        return $this->success('Vendors fetched successfully', $this->repository->findAll($request->get_params(), ['id', 'vendor_name'], ['vendor_name', 'company_name']));
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['vendor_name'])) return $this->error('vendor_name is required.');

        $data = [
            'vendor_name' => sanitize_text_field($params['vendor_name']),
            'company_name' => isset($params['company_name']) ? sanitize_text_field($params['company_name']) : '',
            'service_type' => isset($params['service_type']) ? sanitize_text_field($params['service_type']) : 'General',
            'contact_person' => isset($params['contact_person']) ? sanitize_text_field($params['contact_person']) : '',
            'email' => isset($params['email']) ? sanitize_email($params['email']) : '',
            'mobile' => isset($params['mobile']) ? sanitize_text_field($params['mobile']) : '',
            'gst_number' => isset($params['gst_number']) ? sanitize_text_field($params['gst_number']) : '',
            'address' => isset($params['address']) ? sanitize_textarea_field($params['address']) : '',
            'contract_start' => isset($params['contract_start']) && $params['contract_start'] !== '' ? sanitize_text_field($params['contract_start']) : null,
            'contract_end' => isset($params['contract_end']) && $params['contract_end'] !== '' ? sanitize_text_field($params['contract_end']) : null,
            'sla_terms' => isset($params['sla_terms']) ? sanitize_textarea_field($params['sla_terms']) : '',
            'rating' => isset($params['rating']) ? floatval($params['rating']) : 0.00,
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s']);
        return $this->success('Vendor registered successfully', array_merge(['id' => $id], $data), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $vendor = $this->repository->findById($id);
        if (!$vendor) return $this->error('Vendor not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['vendor_name'])) { $update['vendor_name'] = sanitize_text_field($params['vendor_name']); $formats[] = '%s'; }
        if (isset($params['company_name'])) { $update['company_name'] = sanitize_text_field($params['company_name']); $formats[] = '%s'; }
        if (isset($params['service_type'])) { $update['service_type'] = sanitize_text_field($params['service_type']); $formats[] = '%s'; }
        if (isset($params['contact_person'])) { $update['contact_person'] = sanitize_text_field($params['contact_person']); $formats[] = '%s'; }
        if (isset($params['email'])) { $update['email'] = sanitize_email($params['email']); $formats[] = '%s'; }
        if (isset($params['mobile'])) { $update['mobile'] = sanitize_text_field($params['mobile']); $formats[] = '%s'; }
        if (isset($params['gst_number'])) { $update['gst_number'] = sanitize_text_field($params['gst_number']); $formats[] = '%s'; }
        if (isset($params['address'])) { $update['address'] = sanitize_textarea_field($params['address']); $formats[] = '%s'; }
        if (isset($params['contract_start'])) { $update['contract_start'] = $params['contract_start'] !== '' ? sanitize_text_field($params['contract_start']) : null; $formats[] = '%s'; }
        if (isset($params['contract_end'])) { $update['contract_end'] = $params['contract_end'] !== '' ? sanitize_text_field($params['contract_end']) : null; $formats[] = '%s'; }
        if (isset($params['sla_terms'])) { $update['sla_terms'] = sanitize_textarea_field($params['sla_terms']); $formats[] = '%s'; }
        if (isset($params['rating'])) { $update['rating'] = floatval($params['rating']); $formats[] = '%f'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->repository->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update vendor.');
        return $this->success('Vendor updated successfully', $this->repository->findById($id));
    }

    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $vendor = $this->repository->findById($id);
        if (!$vendor) return $this->error('Vendor not found.', [], 404);

        $this->repository->delete($id);
        return $this->success('Vendor deleted successfully');
    }

    public function indexPayments(WP_REST_Request $request) {
        $paymentRepo = new VendorPaymentRepository();
        return $this->success('Vendor payments fetched successfully', $paymentRepo->findAll($request->get_params(), ['id', 'payment_date'], []));
    }

    public function createPayment(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['vendor_id']) || empty($params['amount'])) return $this->error('vendor_id and amount are required.');

        $paymentRepo = new VendorPaymentRepository();
        $data = [
            'vendor_id' => intval($params['vendor_id']),
            'amount' => floatval($params['amount']),
            'payment_date' => isset($params['payment_date']) ? sanitize_text_field($params['payment_date']) : current_time('Y-m-d'),
            'payment_method' => isset($params['payment_method']) ? sanitize_text_field($params['payment_method']) : 'UPI',
            'reference_no' => isset($params['reference_no']) ? sanitize_text_field($params['reference_no']) : '',
            'description' => isset($params['description']) ? sanitize_textarea_field($params['description']) : '',
            'status' => 'PAID',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $paymentRepo->create($data, ['%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) return $this->error('Failed to log vendor payment.');
        return $this->success('Vendor payment logged successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updatePayment(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $paymentRepo = new VendorPaymentRepository();
        $payment = $paymentRepo->findById($id);
        if (!$payment) return $this->error('Payment record not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['amount'])) { $update['amount'] = floatval($params['amount']); $formats[] = '%f'; }
        if (isset($params['payment_date'])) { $update['payment_date'] = sanitize_text_field($params['payment_date']); $formats[] = '%s'; }
        if (isset($params['payment_method'])) { $update['payment_method'] = sanitize_text_field($params['payment_method']); $formats[] = '%s'; }
        if (isset($params['reference_no'])) { $update['reference_no'] = sanitize_text_field($params['reference_no']); $formats[] = '%s'; }
        if (isset($params['description'])) { $update['description'] = sanitize_textarea_field($params['description']); $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $paymentRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update vendor payment.');
        return $this->success('Vendor payment updated successfully', $paymentRepo->findById($id));
    }

    public function deletePayment(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $paymentRepo = new VendorPaymentRepository();
        $payment = $paymentRepo->findById($id);
        if (!$payment) return $this->error('Payment record not found.', [], 404);

        $paymentRepo->delete($id);
        return $this->success('Vendor payment deleted successfully');
    }
}
