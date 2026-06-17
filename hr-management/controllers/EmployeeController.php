<?php
namespace HrManagementApi\Controllers;

use HrManagementApi\Repositories\EmployeeRepository;
use HrManagementApi\Repositories\LeaveRepository;
use HrManagementApi\Services\AuthService;
use WP_REST_Request;

class EmployeeController extends BaseController {
    private $employeeRepository;
    private $leaveRepository;

    public function __construct() {
        $this->employeeRepository = new EmployeeRepository();
        $this->leaveRepository = new LeaveRepository();
    }

    /**
     * GET /employees
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'user_id', 'department', 'designation', 'date_of_joining', 'status'];
        $search_fields = ['department', 'designation', 'pf_number', 'esi_number', 'status'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['department'])) {
            $extra_filters['department'] = sanitize_text_field($params['department']);
        }

        $results = $this->employeeRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

        foreach ($results['data'] as &$row) {
            $user = get_userdata($row['user_id']);
            $row['name'] = $user ? $user->display_name : 'Unknown User';
            $row['email'] = $user ? $user->user_email : '';
            $row['username'] = $user ? $user->user_login : '';
            
            // Link leave balances
            $balance = $this->leaveRepository->getLeaveBalance($row['id']);
            $row['leave_balances'] = $balance ?: [];
        }

        return $this->success('Employees list retrieved successfully.', $results);
    }

    /**
     * GET /employees/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $emp = $this->employeeRepository->findById($id);

        if (!$emp) {
            return $this->error('Employee record not found.', [], 404);
        }

        $user = get_userdata($emp['user_id']);
        $emp['name'] = $user ? $user->display_name : 'Unknown User';
        $emp['email'] = $user ? $user->user_email : '';
        $emp['username'] = $user ? $user->user_login : '';
        
        $balance = $this->leaveRepository->getLeaveBalance($id);
        $emp['leave_balances'] = $balance ?: [];

        return $this->success('Employee profile retrieved successfully.', $emp);
    }

    /**
     * PUT /employees/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $emp = $this->employeeRepository->findById($id);

        if (!$emp) {
            return $this->error('Employee record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'department' => '%s',
            'designation' => '%s',
            'date_of_joining' => '%s',
            'pf_number' => '%s',
            'esi_number' => '%s',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                $data[$field] = sanitize_text_field($params[$field]);
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No fields to update.');
        }

        $updated = $this->employeeRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update employee metadata.');
        }

        AuthService::logActivity(get_current_user_id(), 'EMPLOYEE_UPDATE', "Updated extended metadata profile of employee ID: $id");

        return $this->success('Employee details updated successfully.', $this->employeeRepository->findById($id));
    }

    /**
     * DELETE /employees/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $emp = $this->employeeRepository->findById($id);

        if (!$emp) {
            return $this->error('Employee record not found.', [], 404);
        }

        // Check if deleting self
        if ($emp['user_id'] === get_current_user_id()) {
            return $this->error('You cannot delete your own employee profile.');
        }

        $deleted = $this->employeeRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete employee record.');
        }

        // Set WordPress user status to BLOCKED or delete WP user
        update_user_meta($emp['user_id'], 'hr_user_status', 'BLOCKED');

        AuthService::logActivity(get_current_user_id(), 'EMPLOYEE_DELETE', "Soft deleted employee profile ID: $id (WP User ID: {$emp['user_id']})");

        return $this->success('Employee profile deleted successfully and associated WP User blocked.');
    }
}
