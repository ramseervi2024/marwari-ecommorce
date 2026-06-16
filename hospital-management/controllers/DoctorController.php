<?php
namespace HospitalManagementApi\Controllers;

use HospitalManagementApi\Repositories\DoctorRepository;
use HospitalManagementApi\Services\AuthService;
use WP_REST_Request;

class DoctorController extends BaseController {
    private $doctorRepository;

    public function __construct() {
        $this->doctorRepository = new DoctorRepository();
    }

    /**
     * GET /doctors
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'doctor_code', 'name', 'specialization', 'consultation_fee', 'status'];
        $search_fields = ['doctor_code', 'name', 'specialization', 'qualification', 'mobile', 'email'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['specialization'])) {
            $extra_filters['specialization'] = sanitize_text_field($params['specialization']);
        }

        // Restrict Doctor role to viewing only active doctors or their own record
        if (current_user_can('write_prescriptions') && !current_user_can('manage_hospital')) {
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            // Non-admin doctors can search everything but we enforce active status filters
            $extra_filters['status'] = 'ACTIVE';
        }

        $results = $this->doctorRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Doctors retrieved successfully.', $results);
    }

    /**
     * GET /doctors/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $doctor = $this->doctorRepository->findById($id);

        if (!$doctor) {
            return $this->error('Doctor not found.', [], 404);
        }

        return $this->success('Doctor retrieved successfully.', $doctor);
    }

    /**
     * POST /doctors
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['name']) || empty($params['specialization']) || empty($params['mobile']) || empty($params['email'])) {
            return $this->error('Validation failed: name, specialization, mobile, and email are required.');
        }

        // Generate doctor code
        $doctor_code = 'DOC' . date('Y') . sprintf('%04d', rand(1000, 9999));
        while ($this->doctorRepository->existsDoctorCode($doctor_code)) {
            $doctor_code = 'DOC' . date('Y') . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'doctor_code' => $doctor_code,
            'name' => sanitize_text_field($params['name']),
            'specialization' => sanitize_text_field($params['specialization']),
            'qualification' => sanitize_text_field($params['qualification'] ?? ''),
            'mobile' => sanitize_text_field($params['mobile']),
            'email' => sanitize_email($params['email']),
            'consultation_fee' => floatval($params['consultation_fee'] ?? 0.00),
            'experience' => intval($params['experience'] ?? 0),
            'schedule' => isset($params['schedule']) ? sanitize_textarea_field($params['schedule']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%f', '%d', '%s', '%s'];
        $inserted_id = $this->doctorRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create doctor record.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCTOR_CREATE', "Created doctor code $doctor_code ($inserted_id)");

        return $this->success('Doctor created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /doctors/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $doctor = $this->doctorRepository->findById($id);

        if (!$doctor) {
            return $this->error('Doctor not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $string_fields = ['name', 'specialization', 'qualification', 'mobile', 'email', 'schedule', 'status'];
        foreach ($string_fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                } else if ($field === 'schedule') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = '%s';
            }
        }

        if (isset($params['consultation_fee'])) {
            $data['consultation_fee'] = floatval($params['consultation_fee']);
            $formats[] = '%f';
        }

        if (isset($params['experience'])) {
            $data['experience'] = intval($params['experience']);
            $formats[] = '%d';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->doctorRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update doctor.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCTOR_UPDATE', "Updated doctor record ID: $id");

        return $this->success('Doctor updated successfully.', $this->doctorRepository->findById($id));
    }

    /**
     * DELETE /doctors/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $doctor = $this->doctorRepository->findById($id);

        if (!$doctor) {
            return $this->error('Doctor not found.', [], 404);
        }

        $deleted = $this->doctorRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete doctor.');
        }

        AuthService::logActivity(get_current_user_id(), 'DOCTOR_DELETE', "Soft deleted doctor ID: $id ($doctor[doctor_code])");

        return $this->success('Doctor deleted successfully.');
    }
}
