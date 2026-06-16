<?php
namespace HospitalManagementApi\Controllers;

use HospitalManagementApi\Repositories\LaboratoryRepository;
use HospitalManagementApi\Services\AuthService;
use WP_REST_Request;

class LaboratoryController extends BaseController {
    private $laboratoryRepository;

    public function __construct() {
        $this->laboratoryRepository = new LaboratoryRepository();
    }

    /**
     * GET /laboratory (List with joins to patient, doctor and test details)
     */
    public function getAll(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_params();

        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $where = ["lr.deleted_at IS NULL"];
        $args = [];

        // Role-based restrictions: Patients can only view their own lab reports
        if (!current_user_can('manage_hospital') && current_user_can('view_own_medical')) {
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            $patient = $wpdb->get_row(
                $wpdb->prepare("SELECT id FROM {$wpdb->prefix}hospital_patients WHERE email = %s AND deleted_at IS NULL LIMIT 1", $user->user_email),
                ARRAY_A
            );
            $patient_id = $patient ? intval($patient['id']) : -1;
            $where[] = "lr.patient_id = %d";
            $args[] = $patient_id;
        } else if (isset($params['patient_id'])) {
            $where[] = "lr.patient_id = %d";
            $args[] = intval($params['patient_id']);
        }

        if (isset($params['doctor_id'])) {
            $where[] = "lr.doctor_id = %d";
            $args[] = intval($params['doctor_id']);
        }

        $where_clause = implode(" AND ", $where);

        $total_query = "SELECT COUNT(*) FROM {$wpdb->prefix}hospital_lab_reports lr WHERE $where_clause";
        $total_count = !empty($args) ? (int)$wpdb->get_var($wpdb->prepare($total_query, $args)) : (int)$wpdb->get_var($total_query);

        $data_query = "SELECT lr.*, p.name as patient_name, p.patient_code, d.name as doctor_name, t.test_name, t.test_code 
                       FROM {$wpdb->prefix}hospital_lab_reports lr
                       JOIN {$wpdb->prefix}hospital_patients p ON lr.patient_id = p.id
                       JOIN {$wpdb->prefix}hospital_doctors d ON lr.doctor_id = d.id
                       JOIN {$wpdb->prefix}hospital_lab_tests t ON lr.test_id = t.id
                       WHERE $where_clause
                       ORDER BY lr.id DESC
                       LIMIT %d OFFSET %d";

        $data_args = array_merge($args, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_query, $data_args), ARRAY_A);

        return $this->success('Lab reports retrieved successfully.', [
            'total' => $total_count,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total_count / $limit),
            'data' => $rows ?: []
        ]);
    }

    /**
     * GET /laboratory/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $report = $this->laboratoryRepository->findById($id);

        if (!$report) {
            return $this->error('Lab report not found.', [], 404);
        }

        return $this->success('Lab report retrieved successfully.', $report);
    }

    /**
     * POST /laboratory
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['patient_id']) || empty($params['doctor_id']) || empty($params['test_id'])) {
            return $this->error('Validation failed: patient_id, doctor_id, and test_id are required.');
        }

        $data = [
            'patient_id' => intval($params['patient_id']),
            'doctor_id' => intval($params['doctor_id']),
            'test_id' => intval($params['test_id']),
            'report_file' => sanitize_text_field($params['report_file'] ?? ''),
            'remarks' => sanitize_textarea_field($params['remarks'] ?? '')
        ];

        $formats = ['%d', '%d', '%d', '%s', '%s'];
        $inserted_id = $this->laboratoryRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create laboratory report.');
        }

        AuthService::logActivity(get_current_user_id(), 'LAB_REPORT_CREATE', "Created lab report record: $inserted_id");

        return $this->success('Lab report created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /laboratory/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $report = $this->laboratoryRepository->findById($id);

        if (!$report) {
            return $this->error('Lab report not found.', [], 404);
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
        if (isset($params['test_id'])) {
            $data['test_id'] = intval($params['test_id']);
            $formats[] = '%d';
        }
        if (isset($params['report_file'])) {
            $data['report_file'] = sanitize_text_field($params['report_file']);
            $formats[] = '%s';
        }
        if (isset($params['remarks'])) {
            $data['remarks'] = sanitize_textarea_field($params['remarks']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->laboratoryRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update laboratory report.');
        }

        AuthService::logActivity(get_current_user_id(), 'LAB_REPORT_UPDATE', "Updated lab report record ID: $id");

        return $this->success('Lab report updated successfully.', $this->laboratoryRepository->findById($id));
    }

    /**
     * DELETE /laboratory/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $report = $this->laboratoryRepository->findById($id);

        if (!$report) {
            return $this->error('Lab report not found.', [], 404);
        }

        $deleted = $this->laboratoryRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete lab report.');
        }

        AuthService::logActivity(get_current_user_id(), 'LAB_REPORT_DELETE', "Soft deleted lab report ID: $id");

        return $this->success('Lab report deleted successfully.');
    }

    /**
     * GET /lab/tests (Tests catalog helper)
     */
    public function getTestsCatalog(WP_REST_Request $request) {
        global $wpdb;
        $rows = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}hospital_lab_tests WHERE deleted_at IS NULL ORDER BY test_name ASC",
            ARRAY_A
        );
        return $this->success('Lab test templates catalog retrieved.', $rows ?: []);
    }
}
