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
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->empRepo->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Employee profile created successfully', array_merge(['id' => $id], $data), 201);
    }

    public function indexAttendance(WP_REST_Request $request) {
        return $this->success('Attendance records fetched successfully', $this->attendRepo->findAll($request->get_params(), ['id', 'attendance_date'], []));
    }
}
