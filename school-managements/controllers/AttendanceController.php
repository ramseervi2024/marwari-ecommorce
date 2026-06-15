<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\AttendanceRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class AttendanceController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new AttendanceRepository();
    }

    /**
     * GET /attendance/students
     */
    public function getStudentAttendance(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = [];
        if (!empty($params['student_id'])) {
            $filters['student_id'] = (int)$params['student_id'];
        }
        if (!empty($params['attendance_date'])) {
            $filters['attendance_date'] = sanitize_text_field($params['attendance_date']);
        }
        
        // Ensure student_id is set / not null in results
        global $wpdb;
        $params['search'] = ''; // disable generic search
        $result = $this->repository->findAll($params, ['id', 'attendance_date', 'status'], [], $filters);
        
        // Exclude teacher attendance logs
        $filtered_data = [];
        foreach ($result['data'] as $log) {
            if ($log['student_id'] !== null) {
                $filtered_data[] = $log;
            }
        }
        $result['data'] = $filtered_data;
        $result['total'] = count($filtered_data);

        return $this->success('Student attendance logs fetched successfully', $result);
    }

    /**
     * POST /attendance/students
     */
    public function submitStudentAttendance(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['student_id']) || empty($params['attendance_date']) || empty($params['status'])) {
            return $this->error('Validation failed: student_id, attendance_date, and status are required.');
        }

        $allowed_statuses = ['Present', 'Absent', 'Late', 'Half Day'];
        if (!in_array($params['status'], $allowed_statuses)) {
            return $this->error('Invalid status. Must be Present, Absent, Late, or Half Day.');
        }

        $data = [
            'student_id' => (int)$params['student_id'],
            'teacher_id' => null,
            'attendance_date' => sanitize_text_field($params['attendance_date']),
            'status' => sanitize_text_field($params['status']),
            'remarks' => isset($params['remarks']) ? sanitize_text_field($params['remarks']) : null,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $log_id = $this->repository->create($data, ['%d', '%d', '%s', '%s', '%s', '%s', '%s']);
        if (!$log_id) {
            return $this->error('Failed to submit student attendance.');
        }

        AuthService::logActivity(get_current_user_id(), 'STUDENT_ATTENDANCE', "Recorded attendance for student ID: {$params['student_id']} as {$params['status']}");
        return $this->success('Student attendance recorded successfully', array_merge(['id' => $log_id], $data), 201);
    }

    /**
     * PUT /attendance/students/{id}
     */
    public function updateStudentAttendance(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $log = $this->repository->findById($id);

        if (!$log || $log['student_id'] === null) {
            return $this->error('Student attendance log not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['status'])) {
            $allowed_statuses = ['Present', 'Absent', 'Late', 'Half Day'];
            if (!in_array($params['status'], $allowed_statuses)) {
                return $this->error('Invalid status. Must be Present, Absent, Late, or Half Day.');
            }
            $data['status'] = sanitize_text_field($params['status']);
            $formats[] = '%s';
        }

        if (isset($params['remarks'])) {
            $data['remarks'] = sanitize_text_field($params['remarks']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->repository->update($id, $data, $formats);
        return $this->success('Student attendance log updated successfully', $this->repository->findById($id));
    }

    /**
     * GET /attendance/teachers
     */
    public function getTeacherAttendance(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = [];
        if (!empty($params['teacher_id'])) {
            $filters['teacher_id'] = (int)$params['teacher_id'];
        }
        if (!empty($params['attendance_date'])) {
            $filters['attendance_date'] = sanitize_text_field($params['attendance_date']);
        }
        
        $result = $this->repository->findAll($params, ['id', 'attendance_date', 'status'], [], $filters);
        
        // Exclude student attendance logs
        $filtered_data = [];
        foreach ($result['data'] as $log) {
            if ($log['teacher_id'] !== null) {
                $filtered_data[] = $log;
            }
        }
        $result['data'] = $filtered_data;
        $result['total'] = count($filtered_data);

        return $this->success('Teacher attendance logs fetched successfully', $result);
    }

    /**
     * POST /attendance/teachers
     */
    public function submitTeacherAttendance(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['teacher_id']) || empty($params['attendance_date']) || empty($params['status'])) {
            return $this->error('Validation failed: teacher_id, attendance_date, and status are required.');
        }

        $allowed_statuses = ['Present', 'Absent', 'Late', 'Half Day'];
        if (!in_array($params['status'], $allowed_statuses)) {
            return $this->error('Invalid status. Must be Present, Absent, Late, or Half Day.');
        }

        $data = [
            'student_id' => null,
            'teacher_id' => (int)$params['teacher_id'],
            'attendance_date' => sanitize_text_field($params['attendance_date']),
            'status' => sanitize_text_field($params['status']),
            'remarks' => isset($params['remarks']) ? sanitize_text_field($params['remarks']) : null,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $log_id = $this->repository->create($data, ['%d', '%d', '%s', '%s', '%s', '%s', '%s']);
        if (!$log_id) {
            return $this->error('Failed to submit teacher attendance.');
        }

        AuthService::logActivity(get_current_user_id(), 'TEACHER_ATTENDANCE', "Recorded attendance for teacher ID: {$params['teacher_id']} as {$params['status']}");
        return $this->success('Teacher attendance recorded successfully', array_merge(['id' => $log_id], $data), 201);
    }

    /**
     * PUT /attendance/teachers/{id}
     */
    public function updateTeacherAttendance(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $log = $this->repository->findById($id);

        if (!$log || $log['teacher_id'] === null) {
            return $this->error('Teacher attendance log not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['status'])) {
            $allowed_statuses = ['Present', 'Absent', 'Late', 'Half Day'];
            if (!in_array($params['status'], $allowed_statuses)) {
                return $this->error('Invalid status. Must be Present, Absent, Late, or Half Day.');
            }
            $data['status'] = sanitize_text_field($params['status']);
            $formats[] = '%s';
        }

        if (isset($params['remarks'])) {
            $data['remarks'] = sanitize_text_field($params['remarks']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->repository->update($id, $data, $formats);
        return $this->success('Teacher attendance log updated successfully', $this->repository->findById($id));
    }
}
