<?php
namespace HospitalManagementApi\Controllers;

use HospitalManagementApi\Repositories\BillingRepository;
use HospitalManagementApi\Services\AuthService;
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
        global $wpdb;
        $params = $request->get_params();

        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $where = ["b.deleted_at IS NULL"];
        $args = [];

        // Role-based restrictions: Patients can only view their own bills
        if (!current_user_can('manage_hospital') && current_user_can('view_own_medical')) {
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            $patient = $wpdb->get_row(
                $wpdb->prepare("SELECT id FROM {$wpdb->prefix}hospital_patients WHERE email = %s AND deleted_at IS NULL LIMIT 1", $user->user_email),
                ARRAY_A
            );
            $patient_id = $patient ? intval($patient['id']) : -1;
            $where[] = "b.patient_id = %d";
            $args[] = $patient_id;
        } else if (isset($params['patient_id'])) {
            $where[] = "b.patient_id = %d";
            $args[] = intval($params['patient_id']);
        }

        if (isset($params['status'])) {
            $where[] = "b.status = %s";
            $args[] = sanitize_text_field($params['status']);
        }

        $where_clause = implode(" AND ", $where);

        $total_query = "SELECT COUNT(*) FROM {$wpdb->prefix}hospital_billing b WHERE $where_clause";
        $total_count = !empty($args) ? (int)$wpdb->get_var($wpdb->prepare($total_query, $args)) : (int)$wpdb->get_var($total_query);

        $data_query = "SELECT b.*, p.name as patient_name, p.patient_code 
                       FROM {$wpdb->prefix}hospital_billing b
                       JOIN {$wpdb->prefix}hospital_patients p ON b.patient_id = p.id
                       WHERE $where_clause
                       ORDER BY b.id DESC
                       LIMIT %d OFFSET %d";

        $data_args = array_merge($args, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_query, $data_args), ARRAY_A);

        return $this->success('Billing records retrieved successfully.', [
            'total' => $total_count,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total_count / $limit),
            'data' => $rows ?: []
        ]);
    }

    /**
     * GET /billing/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $billing = $this->billingRepository->findById($id);

        if (!$billing) {
            return $this->error('Invoice not found.', [], 404);
        }

        return $this->success('Invoice retrieved successfully.', $billing);
    }

    /**
     * POST /billing
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['patient_id']) || empty($params['consultation_charges'])) {
            return $this->error('Validation failed: patient_id and consultation_charges are required.');
        }

        $bill_number = 'BILL-' . date('Ymd') . sprintf('%04d', rand(1000, 9999));
        
        $consultation = floatval($params['consultation_charges']);
        $room = floatval($params['room_charges'] ?? 0);
        $lab = floatval($params['lab_charges'] ?? 0);
        $medicine = floatval($params['medicine_charges'] ?? 0);
        $other = floatval($params['other_charges'] ?? 0);
        $discount = floatval($params['discount'] ?? 0);
        $tax = floatval($params['tax'] ?? 0);
        
        $total = ($consultation + $room + $lab + $medicine + $other - $discount) * (1 + $tax/100);
        $paid = floatval($params['paid_amount'] ?? 0);
        $due = max(0, $total - $paid);
        $status = $due <= 0 ? 'PAID' : ($paid > 0 ? 'PARTIAL' : 'PENDING');

        $data = [
            'patient_id' => intval($params['patient_id']),
            'bill_number' => $bill_number,
            'consultation_charges' => $consultation,
            'room_charges' => $room,
            'lab_charges' => $lab,
            'medicine_charges' => $medicine,
            'other_charges' => $other,
            'discount' => $discount,
            'tax' => $tax,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'due_amount' => $due,
            'status' => $status
        ];

        $formats = ['%d', '%s', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%s'];
        $inserted_id = $this->billingRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create billing record.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILL_CREATE', "Created invoice ID $inserted_id ($bill_number)");

        return $this->success('Billing record created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /billing/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $billing = $this->billingRepository->findById($id);

        if (!$billing) {
            return $this->error('Invoice not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fee_keys = ['consultation_charges', 'room_charges', 'lab_charges', 'medicine_charges', 'other_charges', 'discount', 'tax', 'paid_amount'];
        
        $recalculate = false;
        foreach ($fee_keys as $key) {
            if (isset($params[$key])) {
                $billing[$key] = floatval($params[$key]);
                $data[$key] = $billing[$key];
                $formats[] = '%f';
                $recalculate = true;
            }
        }

        if ($recalculate) {
            $total = ($billing['consultation_charges'] + $billing['room_charges'] + $billing['lab_charges'] + $billing['medicine_charges'] + $billing['other_charges'] - $billing['discount']) * (1 + $billing['tax']/100);
            $due = max(0, $total - $billing['paid_amount']);
            $status = $due <= 0 ? 'PAID' : ($billing['paid_amount'] > 0 ? 'PARTIAL' : 'PENDING');

            $data['total_amount'] = $total;
            $data['due_amount'] = $due;
            $data['status'] = $status;
            
            $formats[] = '%f';
            $formats[] = '%f';
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->billingRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILL_UPDATE', "Updated invoice record ID: $id");

        return $this->success('Invoice updated successfully.', $this->billingRepository->findById($id));
    }

    /**
     * DELETE /billing/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $billing = $this->billingRepository->findById($id);

        if (!$billing) {
            return $this->error('Invoice not found.', [], 404);
        }

        $deleted = $this->billingRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete invoice.');
        }

        AuthService::logActivity(get_current_user_id(), 'BILL_DELETE', "Soft deleted invoice ID: $id");

        return $this->success('Invoice deleted successfully.');
    }
}
