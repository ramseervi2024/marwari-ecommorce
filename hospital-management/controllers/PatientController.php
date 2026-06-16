<?php
namespace HospitalManagementApi\Controllers;

use HospitalManagementApi\Repositories\PatientRepository;
use HospitalManagementApi\Services\AuthService;
use WP_REST_Request;

class PatientController extends BaseController {
    private $patientRepository;

    public function __construct() {
        $this->patientRepository = new PatientRepository();
    }

    /**
     * GET /patients
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'patient_code', 'name', 'status', 'created_at'];
        $search_fields = ['patient_code', 'name', 'mobile', 'email', 'blood_group'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['blood_group'])) {
            $extra_filters['blood_group'] = sanitize_text_field($params['blood_group']);
        }

        // Enforce doctor/patient restrictions if not Super Admin / Receptionist
        if (!current_user_can('manage_hospital') && current_user_can('view_own_medical')) {
            // Logged user is a patient, restrict search to own record
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            $extra_filters['email'] = $user->user_email;
        }

        $results = $this->patientRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Patients retrieved successfully.', $results);
    }

    /**
     * GET /patients/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $patient = $this->patientRepository->findById($id);

        if (!$patient) {
            return $this->error('Patient not found.', [], 404);
        }

        // Restrict patient role to viewing their own record
        if (!current_user_can('manage_hospital') && current_user_can('view_own_medical')) {
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            if ($user->user_email !== $patient['email']) {
                return $this->error('Access Forbidden: You can only view your own records.', [], 403);
            }
        }

        return $this->success('Patient retrieved successfully.', $patient);
    }

    /**
     * POST /patients
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['name'])) {
            return $this->error('Validation failed: name is required.');
        }

        // Generate custom patient code
        $patient_code = 'PAT' . date('Y') . sprintf('%04d', rand(1000, 9999));
        while ($this->patientRepository->existsPatientCode($patient_code)) {
            $patient_code = 'PAT' . date('Y') . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'patient_code' => $patient_code,
            'name' => sanitize_text_field($params['name']),
            'gender' => sanitize_text_field($params['gender'] ?? ''),
            'dob' => !empty($params['dob']) ? sanitize_text_field($params['dob']) : null,
            'mobile' => sanitize_text_field($params['mobile'] ?? ''),
            'email' => sanitize_email($params['email'] ?? ''),
            'address' => sanitize_textarea_field($params['address'] ?? ''),
            'blood_group' => sanitize_text_field($params['blood_group'] ?? ''),
            'emergency_contact' => sanitize_text_field($params['emergency_contact'] ?? ''),
            'insurance_number' => sanitize_text_field($params['insurance_number'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->patientRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create patient.');
        }

        AuthService::logActivity(get_current_user_id(), 'PATIENT_CREATE', "Created patient code $patient_code ($inserted_id)");

        return $this->success('Patient created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /patients/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $patient = $this->patientRepository->findById($id);

        if (!$patient) {
            return $this->error('Patient not found.', [], 404);
        }

        $params = $request->get_json_params();

        $data = [];
        $formats = [];
        
        $fields = ['name', 'gender', 'dob', 'mobile', 'email', 'address', 'blood_group', 'emergency_contact', 'insurance_number', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                } else if ($field === 'address') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = '%s';
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->patientRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update patient.');
        }

        AuthService::logActivity(get_current_user_id(), 'PATIENT_UPDATE', "Updated patient record ID: $id");

        return $this->success('Patient updated successfully.', $this->patientRepository->findById($id));
    }

    /**
     * DELETE /patients/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $patient = $this->patientRepository->findById($id);

        if (!$patient) {
            return $this->error('Patient not found.', [], 404);
        }

        $deleted = $this->patientRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete patient.');
        }

        AuthService::logActivity(get_current_user_id(), 'PATIENT_DELETE', "Soft deleted patient ID: $id ($patient[patient_code])");

        return $this->success('Patient deleted successfully.');
    }

    /**
     * GET /medical-records (Aggregated electronic health record)
     */
    public function getMedicalRecords(WP_REST_Request $request) {
        global $wpdb;
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        
        $patient_id = 0;
        
        if (!current_user_can('manage_hospital') && current_user_can('view_own_medical')) {
            // Patient user: Find corresponding patient record
            $patient = $wpdb->get_row(
                $wpdb->prepare("SELECT id FROM {$wpdb->prefix}hospital_patients WHERE email = %s AND deleted_at IS NULL LIMIT 1", $user->user_email),
                ARRAY_A
            );
            if (!$patient) {
                return $this->error('Patient profile not linked to user account.', [], 404);
            }
            $patient_id = intval($patient['id']);
        } else {
            // Clinician/Admin: Can pass query param patient_id
            $patient_id = intval($request->get_param('patient_id'));
            if (!$patient_id) {
                return $this->error('Validation failed: patient_id is required.');
            }
        }

        // Fetch Prescriptions
        $prescriptions = $wpdb->get_results(
            $wpdb->prepare("SELECT p.*, d.name as doctor_name FROM {$wpdb->prefix}hospital_prescriptions p JOIN {$wpdb->prefix}hospital_doctors d ON p.doctor_id = d.id WHERE p.patient_id = %d AND p.deleted_at IS NULL ORDER BY p.id DESC", $patient_id),
            ARRAY_A
        );

        // Fetch OPD Visits
        $opd = $wpdb->get_results(
            $wpdb->prepare("SELECT o.*, d.name as doctor_name FROM {$wpdb->prefix}hospital_opd o JOIN {$wpdb->prefix}hospital_doctors d ON o.doctor_id = d.id WHERE o.patient_id = %d AND o.deleted_at IS NULL ORDER BY o.id DESC", $patient_id),
            ARRAY_A
        );

        // Fetch IPD Admissions
        $ipd = $wpdb->get_results(
            $wpdb->prepare("SELECT i.*, d.name as doctor_name FROM {$wpdb->prefix}hospital_ipd i JOIN {$wpdb->prefix}hospital_doctors d ON i.doctor_id = d.id WHERE i.patient_id = %d AND i.deleted_at IS NULL ORDER BY i.id DESC", $patient_id),
            ARRAY_A
        );

        // Fetch Lab Reports
        $lab = $wpdb->get_results(
            $wpdb->prepare("SELECT r.*, d.name as doctor_name, t.test_name, t.test_code FROM {$wpdb->prefix}hospital_lab_reports r JOIN {$wpdb->prefix}hospital_doctors d ON r.doctor_id = d.id JOIN {$wpdb->prefix}hospital_lab_tests t ON r.test_id = t.id WHERE r.patient_id = %d AND r.deleted_at IS NULL ORDER BY r.id DESC", $patient_id),
            ARRAY_A
        );

        // Fetch Bills
        $bills = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}hospital_billing WHERE patient_id = %d AND deleted_at IS NULL ORDER BY id DESC", $patient_id),
            ARRAY_A
        );

        return $this->success('Aggregated health records retrieved.', [
            'patient_id' => $patient_id,
            'prescriptions' => $prescriptions ?: [],
            'opd_visits' => $opd ?: [],
            'ipd_admissions' => $ipd ?: [],
            'lab_reports' => $lab ?: [],
            'billing_invoices' => $bills ?: []
        ]);
    }
}
