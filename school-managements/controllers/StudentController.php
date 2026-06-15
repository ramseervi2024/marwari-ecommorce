<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\StudentRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class StudentController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new StudentRepository();
    }

    /**
     * GET /students
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'admission_no', 'roll_no', 'first_name', 'last_name', 'status'];
        $search_fields = ['admission_no', 'roll_no', 'first_name', 'last_name', 'email', 'mobile'];
        
        $extra_filters = [];
        if (!empty($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (!empty($params['class_id'])) {
            $extra_filters['class_id'] = (int)$params['class_id'];
        }
        if (!empty($params['section_id'])) {
            $extra_filters['section_id'] = (int)$params['section_id'];
        }

        $result = $this->repository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Students fetched successfully', $result);
    }

    /**
     * GET /students/{id}
     */
    public function show(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $student = $this->repository->findById($id);

        if (!$student) {
            return $this->error('Student not found.', [], 404);
        }

        return $this->success('Student details fetched successfully', $student);
    }

    /**
     * POST /students
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['admission_no']) || empty($params['first_name']) || empty($params['last_name'])) {
            return $this->error('Validation failed: admission_no, first_name, last_name are required.');
        }

        $admission_no = sanitize_text_field($params['admission_no']);
        if ($this->repository->existsAdmissionNo($admission_no)) {
            return $this->error("Admission number '$admission_no' already exists.");
        }

        $data = [
            'admission_no' => $admission_no,
            'roll_no' => isset($params['roll_no']) ? sanitize_text_field($params['roll_no']) : null,
            'first_name' => sanitize_text_field($params['first_name']),
            'last_name' => sanitize_text_field($params['last_name']),
            'gender' => isset($params['gender']) ? sanitize_text_field($params['gender']) : null,
            'dob' => isset($params['dob']) ? sanitize_text_field($params['dob']) : null,
            'mobile' => isset($params['mobile']) ? sanitize_text_field($params['mobile']) : null,
            'email' => isset($params['email']) ? sanitize_email($params['email']) : null,
            'address' => isset($params['address']) ? sanitize_textarea_field($params['address']) : null,
            'class_id' => isset($params['class_id']) ? (int)$params['class_id'] : null,
            'section_id' => isset($params['section_id']) ? (int)$params['section_id'] : null,
            'parent_id' => isset($params['parent_id']) ? (int)$params['parent_id'] : null,
            'photo' => isset($params['photo']) ? sanitize_text_field($params['photo']) : null,
            'status' => isset($params['status']) ? sanitize_text_field($params['status']) : 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s'];
        $student_id = $this->repository->create($data, $formats);

        if (!$student_id) {
            return $this->error('Failed to register student.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_STUDENT', "Registered student $admission_no (ID: $student_id)");

        return $this->success('Student registered successfully', array_merge(['id' => $student_id], $data), 201);
    }

    /**
     * PUT /students/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $student = $this->repository->findById($id);

        if (!$student) {
            return $this->error('Student not found.', [], 404);
        }

        $params = $request->get_json_params();
        $update_data = [];
        $formats = [];

        if (isset($params['admission_no'])) {
            $admission_no = sanitize_text_field($params['admission_no']);
            if ($this->repository->existsAdmissionNo($admission_no, $id)) {
                return $this->error("Admission number '$admission_no' already exists.");
            }
            $update_data['admission_no'] = $admission_no;
            $formats[] = '%s';
        }

        $allowed_fields = [
            'roll_no' => '%s',
            'first_name' => '%s',
            'last_name' => '%s',
            'gender' => '%s',
            'dob' => '%s',
            'mobile' => '%s',
            'email' => '%s',
            'address' => '%s',
            'class_id' => '%d',
            'section_id' => '%d',
            'parent_id' => '%d',
            'photo' => '%s',
            'status' => '%s'
        ];

        foreach ($allowed_fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%d') {
                    $update_data[$field] = $params[$field] !== null ? (int)$params[$field] : null;
                } elseif ($field === 'email') {
                    $update_data[$field] = sanitize_email($params[$field]);
                } elseif ($field === 'address') {
                    $update_data[$field] = sanitize_textarea_field($params[$field]);
                } else {
                    $update_data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($update_data)) {
            return $this->error('No parameters provided for update.');
        }

        $update_data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->repository->update($id, $update_data, $formats);

        if (!$success) {
            return $this->error('Failed to update student details.');
        }

        AuthService::logActivity(get_current_user_id(), 'UPDATE_STUDENT', "Updated student details ID: $id");

        return $this->success('Student details updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /students/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $student = $this->repository->findById($id);

        if (!$student) {
            return $this->error('Student not found.', [], 404);
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            return $this->error('Failed to delete student.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELETE_STUDENT', "Soft deleted student ID: $id");

        return $this->success('Student soft deleted successfully');
    }
}
