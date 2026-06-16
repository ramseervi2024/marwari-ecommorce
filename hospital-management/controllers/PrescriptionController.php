<?php
namespace HospitalManagementApi\Controllers;

use HospitalManagementApi\Services\AuthService;
use WP_REST_Request;

class PrescriptionController extends BaseController {

    /**
     * GET /prescriptions
     */
    public function getAll(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_params();

        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $where = ["pr.deleted_at IS NULL"];
        $args = [];

        // Role-based restrictions: Patients can only view their own prescriptions
        if (!current_user_can('manage_hospital') && current_user_can('view_own_medical')) {
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            $patient = $wpdb->get_row(
                $wpdb->prepare("SELECT id FROM {$wpdb->prefix}hospital_patients WHERE email = %s AND deleted_at IS NULL LIMIT 1", $user->user_email),
                ARRAY_A
            );
            $patient_id = $patient ? intval($patient['id']) : -1;
            $where[] = "pr.patient_id = %d";
            $args[] = $patient_id;
        } else if (isset($params['patient_id'])) {
            $where[] = "pr.patient_id = %d";
            $args[] = intval($params['patient_id']);
        }

        if (isset($params['doctor_id'])) {
            $where[] = "pr.doctor_id = %d";
            $args[] = intval($params['doctor_id']);
        }

        $where_clause = implode(" AND ", $where);

        $total_query = "SELECT COUNT(*) FROM {$wpdb->prefix}hospital_prescriptions pr WHERE $where_clause";
        $total_count = !empty($args) ? (int)$wpdb->get_var($wpdb->prepare($total_query, $args)) : (int)$wpdb->get_var($total_query);

        $data_query = "SELECT pr.*, p.name as patient_name, p.patient_code, d.name as doctor_name, d.specialization 
                       FROM {$wpdb->prefix}hospital_prescriptions pr
                       JOIN {$wpdb->prefix}hospital_patients p ON pr.patient_id = p.id
                       JOIN {$wpdb->prefix}hospital_doctors d ON pr.doctor_id = d.id
                       WHERE $where_clause
                       ORDER BY pr.id DESC
                       LIMIT %d OFFSET %d";

        $data_args = array_merge($args, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_query, $data_args), ARRAY_A);

        return $this->success('Prescriptions retrieved successfully.', [
            'total' => $total_count,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total_count / $limit),
            'data' => $rows ?: []
        ]);
    }

    /**
     * GET /prescriptions/:id
     */
    public function getById(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));

        $prescription = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}hospital_prescriptions WHERE id = %d AND deleted_at IS NULL", $id),
            ARRAY_A
        );

        if (!$prescription) {
            return $this->error('Prescription not found.', [], 404);
        }

        return $this->success('Prescription retrieved successfully.', $prescription);
    }

    /**
     * POST /prescriptions
     */
    public function create(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();

        if (empty($params['patient_id']) || empty($params['doctor_id']) || empty($params['medicine'])) {
            return $this->error('Validation failed: patient_id, doctor_id, and medicine are required.');
        }

        $data = [
            'patient_id' => intval($params['patient_id']),
            'doctor_id' => intval($params['doctor_id']),
            'medicine' => sanitize_text_field($params['medicine']),
            'dosage' => sanitize_text_field($params['dosage'] ?? ''),
            'duration' => sanitize_text_field($params['duration'] ?? ''),
            'instructions' => sanitize_textarea_field($params['instructions'] ?? '')
        ];

        $result = $wpdb->insert($wpdb->prefix . 'hospital_prescriptions', $data, ['%d', '%d', '%s', '%s', '%s', '%s']);

        if ($result === false) {
            return $this->error('Failed to create prescription.');
        }

        $inserted_id = intval($wpdb->insert_id);
        AuthService::logActivity(get_current_user_id(), 'PRESCRIPTION_CREATE', "Created prescription ID: $inserted_id");

        return $this->success('Prescription created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /prescriptions/:id
     */
    public function update(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));

        $prescription = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}hospital_prescriptions WHERE id = %d AND deleted_at IS NULL", $id),
            ARRAY_A
        );

        if (!$prescription) {
            return $this->error('Prescription not found.', [], 404);
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
        if (isset($params['medicine'])) {
            $data['medicine'] = sanitize_text_field($params['medicine']);
            $formats[] = '%s';
        }
        if (isset($params['dosage'])) {
            $data['dosage'] = sanitize_text_field($params['dosage']);
            $formats[] = '%s';
        }
        if (isset($params['duration'])) {
            $data['duration'] = sanitize_text_field($params['duration']);
            $formats[] = '%s';
        }
        if (isset($params['instructions'])) {
            $data['instructions'] = sanitize_textarea_field($params['instructions']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $wpdb->update(
            $wpdb->prefix . 'hospital_prescriptions',
            $data,
            ['id' => $id],
            $formats,
            ['%d']
        );

        if ($updated === false) {
            return $this->error('Failed to update prescription.');
        }

        AuthService::logActivity(get_current_user_id(), 'PRESCRIPTION_UPDATE', "Updated prescription record ID: $id");

        return $this->success('Prescription updated successfully.', $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}hospital_prescriptions WHERE id = %d AND deleted_at IS NULL", $id),
            ARRAY_A
        ));
    }

    /**
     * DELETE /prescriptions/:id
     */
    public function delete(WP_REST_Request $request) {
        global $wpdb;
        $id = intval($request->get_param('id'));

        $prescription = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}hospital_prescriptions WHERE id = %d AND deleted_at IS NULL", $id),
            ARRAY_A
        );

        if (!$prescription) {
            return $this->error('Prescription not found.', [], 404);
        }

        $deleted = $wpdb->update(
            $wpdb->prefix . 'hospital_prescriptions',
            ['deleted_at' => current_time('mysql')],
            ['id' => $id],
            ['%s'],
            ['%d']
        );

        if ($deleted === false) {
            return $this->error('Failed to delete prescription.');
        }

        AuthService::logActivity(get_current_user_id(), 'PRESCRIPTION_DELETE', "Soft deleted prescription ID: $id");

        return $this->success('Prescription deleted successfully.');
    }
}
