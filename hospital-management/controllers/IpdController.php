<?php
namespace HospitalManagementApi\Controllers;

use HospitalManagementApi\Repositories\IpdRepository;
use HospitalManagementApi\Services\AuthService;
use WP_REST_Request;

class IpdController extends BaseController {
    private $ipdRepository;

    public function __construct() {
        $this->ipdRepository = new IpdRepository();
    }

    /**
     * GET /ipd (List with joins to patient and doctor names)
     */
    public function getAll(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_params();
        
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;
        
        $where = ["i.deleted_at IS NULL"];
        $args = [];

        // Role-based restrictions: Patients can only view their own IPD records
        if (!current_user_can('manage_hospital') && current_user_can('view_own_medical')) {
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            $patient = $wpdb->get_row(
                $wpdb->prepare("SELECT id FROM {$wpdb->prefix}hospital_patients WHERE email = %s AND deleted_at IS NULL LIMIT 1", $user->user_email),
                ARRAY_A
            );
            $patient_id = $patient ? intval($patient['id']) : -1;
            $where[] = "i.patient_id = %d";
            $args[] = $patient_id;
        } else if (isset($params['patient_id'])) {
            $where[] = "i.patient_id = %d";
            $args[] = intval($params['patient_id']);
        }

        if (isset($params['doctor_id'])) {
            $where[] = "i.doctor_id = %d";
            $args[] = intval($params['doctor_id']);
        }
        if (isset($params['status'])) {
            $where[] = "i.status = %s";
            $args[] = sanitize_text_field($params['status']);
        }

        $where_clause = implode(" AND ", $where);

        $total_query = "SELECT COUNT(*) FROM {$wpdb->prefix}hospital_ipd i WHERE $where_clause";
        $total_count = !empty($args) ? (int)$wpdb->get_var($wpdb->prepare($total_query, $args)) : (int)$wpdb->get_var($total_query);

        $data_query = "SELECT i.*, p.name as patient_name, p.patient_code, d.name as doctor_name, d.specialization 
                       FROM {$wpdb->prefix}hospital_ipd i 
                       JOIN {$wpdb->prefix}hospital_patients p ON i.patient_id = p.id 
                       JOIN {$wpdb->prefix}hospital_doctors d ON i.doctor_id = d.id 
                       WHERE $where_clause 
                       ORDER BY i.admission_date DESC 
                       LIMIT %d OFFSET %d";
                       
        $data_args = array_merge($args, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_query, $data_args), ARRAY_A);

        return $this->success('IPD records retrieved successfully.', [
            'total' => $total_count,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total_count / $limit),
            'data' => $rows ?: []
        ]);
    }

    /**
     * GET /ipd/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $ipd = $this->ipdRepository->findById($id);

        if (!$ipd) {
            return $this->error('IPD record not found.', [], 404);
        }

        return $this->success('IPD record retrieved successfully.', $ipd);
    }

    /**
     * POST /ipd
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['patient_id']) || empty($params['doctor_id']) || empty($params['admission_date'])) {
            return $this->error('Validation failed: patient_id, doctor_id, and admission_date are required.');
        }

        $data = [
            'patient_id' => intval($params['patient_id']),
            'doctor_id' => intval($params['doctor_id']),
            'admission_date' => sanitize_text_field($params['admission_date']),
            'discharge_date' => !empty($params['discharge_date']) ? sanitize_text_field($params['discharge_date']) : null,
            'ward' => sanitize_text_field($params['ward'] ?? 'General'),
            'room_number' => sanitize_text_field($params['room_number'] ?? ''),
            'bed_number' => sanitize_text_field($params['bed_number'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'ADMITTED')
        ];

        $formats = ['%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->ipdRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to admit patient to IPD.');
        }

        AuthService::logActivity(get_current_user_id(), 'IPD_CREATE', "Admitted patient ID to IPD: $inserted_id");

        return $this->success('Patient admitted to IPD successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /ipd/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $ipd = $this->ipdRepository->findById($id);

        if (!$ipd) {
            return $this->error('IPD admission record not found.', [], 404);
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
        if (isset($params['admission_date'])) {
            $data['admission_date'] = sanitize_text_field($params['admission_date']);
            $formats[] = '%s';
        }
        if (isset($params['discharge_date'])) {
            $data['discharge_date'] = sanitize_text_field($params['discharge_date']);
            $formats[] = '%s';
        }
        if (isset($params['ward'])) {
            $data['ward'] = sanitize_text_field($params['ward']);
            $formats[] = '%s';
        }
        if (isset($params['room_number'])) {
            $data['room_number'] = sanitize_text_field($params['room_number']);
            $formats[] = '%s';
        }
        if (isset($params['bed_number'])) {
            $data['bed_number'] = sanitize_text_field($params['bed_number']);
            $formats[] = '%s';
        }
        if (isset($params['status'])) {
            $data['status'] = sanitize_text_field($params['status']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->ipdRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update IPD record.');
        }

        AuthService::logActivity(get_current_user_id(), 'IPD_UPDATE', "Updated IPD record ID: $id");

        return $this->success('IPD record updated successfully.', $this->ipdRepository->findById($id));
    }

    /**
     * DELETE /ipd/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $ipd = $this->ipdRepository->findById($id);

        if (!$ipd) {
            return $this->error('IPD record not found.', [], 404);
        }

        $deleted = $this->ipdRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete IPD record.');
        }

        AuthService::logActivity(get_current_user_id(), 'IPD_DELETE', "Soft deleted IPD record ID: $id");

        return $this->success('IPD record deleted successfully.');
    }
}
