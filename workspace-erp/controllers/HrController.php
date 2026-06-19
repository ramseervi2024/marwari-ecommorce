<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\EmployeeRepository;
use WorkspaceErpApi\Repositories\AttendanceRepository;
use WorkspaceErpApi\Services\AuthService;
use WP_REST_Request;

class HrController extends BaseController {
    private $empRepo;
    private $attendRepo;

    public function __construct() {
        $this->empRepo = new EmployeeRepository();
        $this->attendRepo = new AttendanceRepository();
    }

    public function indexEmployees(WP_REST_Request $request) {
        return $this->success('Employees fetched successfully', $this->empRepo->findAll($request->get_params(), ['id', 'employee_code', 'name'], ['employee_code', 'name', 'department']));
    }

    public function createEmployee(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['name']) || empty($params['employee_code'])) {
            return $this->error('name and employee_code are required.');
        }

        $code = sanitize_text_field($params['employee_code']);
        if ($this->empRepo->existsEmployeeCode($code)) {
            return $this->error("Employee code '$code' already exists.");
        }

        $data = [
            'employee_code' => $code,
            'name' => sanitize_text_field($params['name']),
            'department' => isset($params['department']) ? sanitize_text_field($params['department']) : 'Facilities',
            'designation' => isset($params['designation']) ? sanitize_text_field($params['designation']) : '',
            'mobile' => isset($params['mobile']) ? sanitize_text_field($params['mobile']) : '',
            'email' => isset($params['email']) ? sanitize_email($params['email']) : '',
            'joining_date' => isset($params['joining_date']) ? sanitize_text_field($params['joining_date']) : current_time('Y-m-d'),
            'salary' => isset($params['salary']) ? floatval($params['salary']) : 0.00,
            'shift' => isset($params['shift']) ? sanitize_text_field($params['shift']) : 'DAY',
            'building_id' => isset($params['building_id']) ? intval($params['building_id']) : null,
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->empRepo->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%d', '%s', '%s', '%s']);
        return $this->success('Employee profile created successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateEmployee(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $employee = $this->empRepo->findById($id);
        if (!$employee) return $this->error('Employee not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['name'])) { $update['name'] = sanitize_text_field($params['name']); $formats[] = '%s'; }
        if (isset($params['department'])) { $update['department'] = sanitize_text_field($params['department']); $formats[] = '%s'; }
        if (isset($params['designation'])) { $update['designation'] = sanitize_text_field($params['designation']); $formats[] = '%s'; }
        if (isset($params['mobile'])) { $update['mobile'] = sanitize_text_field($params['mobile']); $formats[] = '%s'; }
        if (isset($params['email'])) { $update['email'] = sanitize_email($params['email']); $formats[] = '%s'; }
        if (isset($params['joining_date'])) { $update['joining_date'] = sanitize_text_field($params['joining_date']); $formats[] = '%s'; }
        if (isset($params['salary'])) { $update['salary'] = floatval($params['salary']); $formats[] = '%f'; }
        if (isset($params['shift'])) { $update['shift'] = sanitize_text_field($params['shift']); $formats[] = '%s'; }
        if (isset($params['building_id'])) { $update['building_id'] = intval($params['building_id']); $formats[] = '%d'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->empRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update employee.');

        AuthService::logActivity(get_current_user_id(), 'UPDATE_EMPLOYEE', "Updated employee ID: $id");
        return $this->success('Employee updated successfully', $this->empRepo->findById($id));
    }

    public function deleteEmployee(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $employee = $this->empRepo->findById($id);
        if (!$employee) return $this->error('Employee not found.', [], 404);

        $this->empRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_EMPLOYEE', "Soft deleted employee ID: $id");
        return $this->success('Employee deleted successfully');
    }

    public function indexAttendance(WP_REST_Request $request) {
        return $this->success('Attendance records fetched successfully', $this->attendRepo->findAll($request->get_params(), ['id', 'attendance_date'], []));
    }

    public function createAttendance(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['employee_id']) || empty($params['attendance_date'])) {
            return $this->error('employee_id and attendance_date are required.');
        }

        $data = [
            'employee_id' => intval($params['employee_id']),
            'attendance_date' => sanitize_text_field($params['attendance_date']),
            'check_in' => isset($params['check_in']) && $params['check_in'] !== '' ? sanitize_text_field($params['check_in']) : null,
            'check_out' => isset($params['check_out']) && $params['check_out'] !== '' ? sanitize_text_field($params['check_out']) : null,
            'status' => isset($params['status']) ? sanitize_text_field($params['status']) : 'PRESENT',
            'remarks' => isset($params['remarks']) ? sanitize_textarea_field($params['remarks']) : '',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->attendRepo->create($data, ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) return $this->error('Failed to log attendance.');
        return $this->success('Attendance record created successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateAttendance(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $record = $this->attendRepo->findById($id);
        if (!$record) return $this->error('Attendance record not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['check_in'])) { $update['check_in'] = sanitize_text_field($params['check_in']); $formats[] = '%s'; }
        if (isset($params['check_out'])) { $update['check_out'] = sanitize_text_field($params['check_out']); $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }
        if (isset($params['remarks'])) { $update['remarks'] = sanitize_textarea_field($params['remarks']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->attendRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update attendance.');
        return $this->success('Attendance updated successfully', $this->attendRepo->findById($id));
    }

    public function deleteAttendance(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $record = $this->attendRepo->findById($id);
        if (!$record) return $this->error('Attendance record not found.', [], 404);

        $this->attendRepo->delete($id);
        return $this->success('Attendance record deleted successfully');
    }
}
