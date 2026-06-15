<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\TeacherRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class TeacherController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new TeacherRepository();
    }

    /**
     * GET /teachers
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'employee_code', 'name', 'salary', 'joining_date', 'status'];
        $search_fields = ['employee_code', 'name', 'email', 'mobile', 'qualification'];
        
        $extra_filters = [];
        if (!empty($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $result = $this->repository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Teachers fetched successfully', $result);
    }

    /**
     * GET /teachers/{id}
     */
    public function show(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $teacher = $this->repository->findById($id);

        if (!$teacher) {
            return $this->error('Teacher not found.', [], 404);
        }

        return $this->success('Teacher details fetched successfully', $teacher);
    }

    /**
     * POST /teachers
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['employee_code']) || empty($params['name']) || empty($params['mobile']) || empty($params['email'])) {
            return $this->error('Validation failed: employee_code, name, mobile, and email are required.');
        }

        $emp_code = sanitize_text_field($params['employee_code']);
        if ($this->repository->existsEmployeeCode($emp_code)) {
            return $this->error("Employee code '$emp_code' already exists.");
        }

        $data = [
            'employee_code' => $emp_code,
            'name' => sanitize_text_field($params['name']),
            'mobile' => sanitize_text_field($params['mobile']),
            'email' => sanitize_email($params['email']),
            'qualification' => isset($params['qualification']) ? sanitize_text_field($params['qualification']) : null,
            'salary' => isset($params['salary']) ? (float)$params['salary'] : 0.00,
            'joining_date' => isset($params['joining_date']) ? sanitize_text_field($params['joining_date']) : null,
            'photo' => isset($params['photo']) ? sanitize_text_field($params['photo']) : null,
            'status' => isset($params['status']) ? sanitize_text_field($params['status']) : 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s'];
        $teacher_id = $this->repository->create($data, $formats);

        if (!$teacher_id) {
            return $this->error('Failed to create teacher.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_TEACHER', "Registered teacher $emp_code (ID: $teacher_id)");

        return $this->success('Teacher created successfully', array_merge(['id' => $teacher_id], $data), 201);
    }

    /**
     * PUT /teachers/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $teacher = $this->repository->findById($id);

        if (!$teacher) {
            return $this->error('Teacher not found.', [], 404);
        }

        $params = $request->get_json_params();
        $update_data = [];
        $formats = [];

        if (isset($params['employee_code'])) {
            $emp_code = sanitize_text_field($params['employee_code']);
            if ($this->repository->existsEmployeeCode($emp_code, $id)) {
                return $this->error("Employee code '$emp_code' already exists.");
            }
            $update_data['employee_code'] = $emp_code;
            $formats[] = '%s';
        }

        $allowed_fields = [
            'name' => '%s',
            'mobile' => '%s',
            'email' => '%s',
            'qualification' => '%s',
            'salary' => '%f',
            'joining_date' => '%s',
            'photo' => '%s',
            'status' => '%s'
        ];

        foreach ($allowed_fields as $field => $format) {
            if (isset($params[$field])) {
                if ($format === '%f') {
                    $update_data[$field] = (float)$params[$field];
                } elseif ($field === 'email') {
                    $update_data[$field] = sanitize_email($params[$field]);
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
            return $this->error('Failed to update teacher.');
        }

        AuthService::logActivity(get_current_user_id(), 'UPDATE_TEACHER', "Updated teacher details ID: $id");

        return $this->success('Teacher updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /teachers/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $teacher = $this->repository->findById($id);

        if (!$teacher) {
            return $this->error('Teacher not found.', [], 404);
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            return $this->error('Failed to delete teacher.');
        }

        AuthService::logActivity(get_current_user_id(), 'DELETE_TEACHER', "Soft deleted teacher ID: $id");

        return $this->success('Teacher soft deleted successfully');
    }
}
