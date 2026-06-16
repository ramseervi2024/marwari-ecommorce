<?php
namespace HospitalManagementApi\Controllers;

use HospitalManagementApi\Repositories\OpdRepository;
use HospitalManagementApi\Services\AuthService;
use WP_REST_Request;

class OpdController extends BaseController {
    private $opdRepository;

    public function __construct() {
        $this->opdRepository = new OpdRepository();
    }

    /**
     * GET /opd (List with joins to patient and doctor names)
     */
    public function getAll(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_params();
        
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;
        
        $where = ["o.deleted_at IS NULL"];
        $args = [];

        // Role-based restrictions: Patients can only view their own OPD visits
        if (!current_user_can('manage_hospital') && current_user_can('view_own_medical')) {
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            $patient = $wpdb->get_row(
                $wpdb->prepare("SELECT id FROM {$wpdb->prefix}hospital_patients WHERE email = %s AND deleted_at IS NULL LIMIT 1", $user->user_email),
                ARRAY_A
            );
            $patient_id = $patient ? intval($patient['id']) : -1;
            $where[] = "o.patient_id = %d";
            $args[] = $patient_id;
        } else if (isset($params['patient_id'])) {
            $where[] = "o.patient_id = %d";
            $args[] = intval($params['patient_id']);
        }

        if (isset($params['doctor_id'])) {
            $where[] = "o.doctor_id = %d";
            $args[] = intval($params['doctor_id']);
        }

        $where_clause = implode(" AND ", $where);

        $total_query = "SELECT COUNT(*) FROM {$wpdb->prefix}hospital_opd o WHERE $where_clause";
        $total_count = !empty($args) ? (int)$wpdb->get_var($wpdb->prepare($total_query, $args)) : (int)$wpdb->get_var($total_query);

        $data_query = "SELECT o.*, p.name as patient_name, p.patient_code, d.name as doctor_name, d.specialization 
                       FROM {$wpdb->prefix}hospital_opd o 
                       JOIN {$wpdb->prefix}hospital_patients p ON o.patient_id = p.id 
                       JOIN {$wpdb->prefix}hospital_doctors d ON o.doctor_id = d.id 
                       WHERE $where_clause 
                       ORDER BY o.visit_date DESC 
                       LIMIT %d OFFSET %d";
                       
        $data_args = array_merge($args, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_query, $data_args), ARRAY_A);

        return $this->success('OPD records retrieved successfully.', [
            'total' => $total_count,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total_count / $limit),
            'data' => $rows ?: []
        ]);
    }

    /**
     * GET /opd/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $opd = $this->opdRepository->findById($id);

        if (!$opd) {
            return $this->error('OPD record not found.', [], 404);
        }

        return $this->success('OPD record retrieved successfully.', $opd);
    }

    /**
     * POST /opd
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['patient_id']) || empty($params['doctor_id']) || empty($params['visit_date'])) {
            return $this->error('Validation failed: patient_id, doctor_id, and visit_date are required.');
        }

        $data = [
            'patient_id' => intval($params['patient_id']),
            'doctor_id' => intval($params['doctor_id']),
            'visit_date' => sanitize_text_field($params['visit_date']),
            'symptoms' => sanitize_textarea_field($params['symptoms'] ?? ''),
            'diagnosis' => sanitize_textarea_field($params['diagnosis'] ?? ''),
            'prescription' => sanitize_textarea_field($params['prescription'] ?? ''),
            'consultation_fee' => floatval($params['consultation_fee'] ?? 0.00)
        ];

        $formats = ['%d', '%d', '%s', '%s', '%s', '%s', '%f'];
        $inserted_id = $this->opdRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create OPD record.');
        }

        AuthService::logActivity(get_current_user_id(), 'OPD_CREATE', "Created OPD record ID: $inserted_id");

        return $this->success('OPD record created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /opd/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $opd = $this->opdRepository->findById($id);

        if (!$opd) {
            return $this->error('OPD record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['patient_id'])) {
            $data['patient_id'] = intval($params['patient_id']);
            $formats[] = '%d';
        }
        if (isset($params['doctor_id'])) {
            $data['doctor_id'] = intval($params['doctor_id']);
            $formats[] = '%d';
        }
        if (isset($params['visit_date'])) {
            $data['visit_date'] = sanitize_text_field($params['visit_date']);
            $formats[] = '%s';
        }
        if (isset($params['symptoms'])) {
            $data['symptoms'] = sanitize_textarea_field($params['symptoms']);
            $formats[] = '%s';
        }
        if (isset($params['diagnosis'])) {
            $data['diagnosis'] = sanitize_textarea_field($params['diagnosis']);
            $formats[] = '%s';
        }
        if (isset($params['prescription'])) {
            $data['prescription'] = sanitize_textarea_field($params['prescription']);
            $formats[] = '%s';
        }
        if (isset($params['consultation_fee'])) {
            $data['consultation_fee'] = floatval($params['consultation_fee']);
            $formats[] = '%f';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->opdRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update OPD record.');
        }

        AuthService::logActivity(get_current_user_id(), 'OPD_UPDATE', "Updated OPD record ID: $id");

        return $this->success('OPD record updated successfully.', $this->opdRepository->findById($id));
    }

    /**
     * DELETE /opd/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $opd = $this->opdRepository->findById($id);

        if (!$opd) {
            return $this->error('OPD record not found.', [], 404);
        }

        $deleted = $this->opdRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete OPD record.');
        }

        AuthService::logActivity(get_current_user_id(), 'OPD_DELETE', "Soft deleted OPD record ID: $id");

        return $this->success('OPD record deleted successfully.');
    }
}
