<?php
namespace HrManagementApi\Controllers;

use HrManagementApi\Repositories\AttendanceRepository;
use HrManagementApi\Repositories\EmployeeRepository;
use HrManagementApi\Services\AuthService;
use WP_REST_Request;

class AttendanceController extends BaseController {
    private $attendanceRepository;
    private $employeeRepository;

    public function __construct() {
        $this->attendanceRepository = new AttendanceRepository();
        $this->employeeRepository = new EmployeeRepository();
    }

    /**
     * GET /attendance
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'date', 'check_in', 'check_out', 'total_hours', 'status'];
        $search_fields = ['status', 'notes'];

        $extra_filters = [];
        if (isset($params['date'])) {
            $extra_filters['date'] = sanitize_text_field($params['date']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        // Employees can only view their own attendance records
        if (!current_user_can('manage_attendance')) {
            $user_id = get_current_user_id();
            $emp = $this->employeeRepository->findByUserId($user_id);
            if (!$emp) {
                return $this->error('Employee profile not found.', [], 404);
            }
            $extra_filters['employee_id'] = $emp['id'];
        } elseif (isset($params['employee_id'])) {
            $extra_filters['employee_id'] = intval($params['employee_id']);
        }

        $results = $this->attendanceRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

        foreach ($results['data'] as &$row) {
            $emp = $this->employeeRepository->findById($row['employee_id']);
            if ($emp) {
                $user = get_userdata($emp['user_id']);
                $row['employee_name'] = $user ? $user->display_name : 'Unknown';
                $row['department'] = $emp['department'];
                $row['designation'] = $emp['designation'];
            } else {
                $row['employee_name'] = 'Unknown';
                $row['department'] = '';
                $row['designation'] = '';
            }
        }

        return $this->success('Attendance records retrieved successfully.', $results);
    }

    /**
     * POST /attendance/check-in
     */
    public function checkIn(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        $emp = $this->employeeRepository->findByUserId($user_id);
        
        if (!$emp) {
            return $this->error('Employee profile not found. Check-in denied.', [], 404);
        }

        $today = date('Y-m-d');
        $existing = $this->attendanceRepository->findByEmployeeIdAndDate($emp['id'], $today);

        if ($existing) {
            return $this->error('You have already checked in today.', $existing);
        }

        $check_in_time = date('H:i:s');
        
        // Late evaluation: if check-in after 10:00 AM
        $status = 'Present';
        if (strtotime($check_in_time) > strtotime('10:00:00')) {
            $status = 'Late';
        }

        $data = [
            'employee_id' => $emp['id'],
            'date' => $today,
            'check_in' => $check_in_time,
            'check_out' => null,
            'total_hours' => 0.00,
            'status' => $status,
            'notes' => 'Self Check-in via Portal'
        ];

        $formats = ['%d', '%s', '%s', '%s', '%f', '%s', '%s'];
        $inserted_id = $this->attendanceRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to record check-in.');
        }

        AuthService::logActivity($user_id, 'ATTENDANCE_CHECK_IN', "Checked in today at $check_in_time (Status: $status)");

        return $this->success('Checked in successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * POST /attendance/check-out
     */
    public function checkOut(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        $emp = $this->employeeRepository->findByUserId($user_id);

        if (!$emp) {
            return $this->error('Employee profile not found. Check-out denied.', [], 404);
        }

        $today = date('Y-m-d');
        $attendance = $this->attendanceRepository->findByEmployeeIdAndDate($emp['id'], $today);

        if (!$attendance) {
            return $this->error('You must check in first before checking out.');
        }

        if (!empty($attendance['check_out'])) {
            return $this->error('You have already checked out today.', $attendance);
        }

        $check_out_time = date('H:i:s');
        
        // Calculate total hours
        $in_sec = strtotime($attendance['check_in']);
        $out_sec = strtotime($check_out_time);
        $diff_hours = round(($out_sec - $in_sec) / 3600, 2);

        // Status evaluation based on hours
        $status = $attendance['status']; // retain Late status if they were late
        if ($diff_hours < 4) {
            $status = 'Half Day'; // Checked out too early
        } elseif ($diff_hours >= 8 && $status !== 'Late') {
            $status = 'Present';
        }

        $data = [
            'check_out' => $check_out_time,
            'total_hours' => $diff_hours,
            'status' => $status
        ];

        $formats = ['%s', '%f', '%s'];
        $updated = $this->attendanceRepository->update($attendance['id'], $data, $formats);

        if (!$updated) {
            return $this->error('Failed to record check-out.');
        }

        AuthService::logActivity($user_id, 'ATTENDANCE_CHECK_OUT', "Checked out today at $check_out_time (Hours: $diff_hours, Status: $status)");

        return $this->success('Checked out successfully.', array_merge($attendance, $data));
    }

    /**
     * PUT /attendance/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $record = $this->attendanceRepository->findById($id);

        if (!$record) {
            return $this->error('Attendance record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'check_in' => '%s',
            'check_out' => '%s',
            'status' => '%s',
            'notes' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                $data[$field] = sanitize_text_field($params[$field]);
                $formats[] = $format;
            }
        }

        // If check-in or check-out is changed, recalculate hours
        $in = $data['check_in'] ?? $record['check_in'];
        $out = $data['check_out'] ?? $record['check_out'];

        if (!empty($in) && !empty($out)) {
            $in_sec = strtotime($in);
            $out_sec = strtotime($out);
            $data['total_hours'] = round(($out_sec - $in_sec) / 3600, 2);
            $formats[] = '%f';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->attendanceRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update attendance details.');
        }

        AuthService::logActivity(get_current_user_id(), 'ATTENDANCE_ADMIN_UPDATE', "Admin adjusted attendance record ID: $id");

        return $this->success('Attendance details adjusted successfully.', $this->attendanceRepository->findById($id));
    }
}
