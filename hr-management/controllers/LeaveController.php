<?php
namespace HrManagementApi\Controllers;

use HrManagementApi\Repositories\LeaveRepository;
use HrManagementApi\Repositories\EmployeeRepository;
use HrManagementApi\Services\AuthService;
use WP_REST_Request;

class LeaveController extends BaseController {
    private $leaveRepository;
    private $employeeRepository;

    public function __construct() {
        $this->leaveRepository = new LeaveRepository();
        $this->employeeRepository = new EmployeeRepository();
    }

    /**
     * GET /leaves
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'start_date', 'end_date', 'status', 'leave_type'];
        $search_fields = ['leave_type', 'status', 'reason', 'comments'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['leave_type'])) {
            $extra_filters['leave_type'] = sanitize_text_field($params['leave_type']);
        }

        // Employees can only view their own leave requests
        if (!current_user_can('manage_leaves')) {
            $user_id = get_current_user_id();
            $emp = $this->employeeRepository->findByUserId($user_id);
            if (!$emp) {
                return $this->error('Employee profile not found.', [], 404);
            }
            $extra_filters['employee_id'] = $emp['id'];
        } elseif (isset($params['employee_id'])) {
            $extra_filters['employee_id'] = intval($params['employee_id']);
        }

        $results = $this->leaveRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

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

            if (!empty($row['approved_by'])) {
                $approver = get_userdata($row['approved_by']);
                $row['approver_name'] = $approver ? $approver->display_name : 'System';
            } else {
                $row['approver_name'] = '';
            }
        }

        return $this->success('Leave applications retrieved successfully.', $results);
    }

    /**
     * GET /leaves/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $leave = $this->leaveRepository->findById($id);

        if (!$leave) {
            return $this->error('Leave application not found.', [], 404);
        }

        // Access check
        if (!current_user_can('manage_leaves')) {
            $user_id = get_current_user_id();
            $emp = $this->employeeRepository->findByUserId($user_id);
            if (!$emp || intval($leave['employee_id']) !== $emp['id']) {
                return $this->error('Access Forbidden: You do not have permission to view this leave.', [], 403);
            }
        }

        $emp = $this->employeeRepository->findById($leave['employee_id']);
        $user = get_userdata($emp['user_id']);
        $leave['employee_name'] = $user ? $user->display_name : 'Unknown';

        return $this->success('Leave details retrieved successfully.', $leave);
    }

    /**
     * POST /leaves
     */
    public function apply(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['leave_type']) || empty($params['start_date']) || empty($params['end_date'])) {
            return $this->error('leave_type, start_date, and end_date are required.');
        }

        $user_id = get_current_user_id();
        $emp = $this->employeeRepository->findByUserId($user_id);
        if (!$emp) {
            return $this->error('Employee record not found. Apply denied.', [], 404);
        }

        $start = sanitize_text_field($params['start_date']);
        $end = sanitize_text_field($params['end_date']);
        $leave_type = sanitize_text_field($params['leave_type']);
        $reason = sanitize_textarea_field($params['reason'] ?? '');

        // Calculate requested days
        $start_sec = strtotime($start);
        $end_sec = strtotime($end);
        if ($end_sec < $start_sec) {
            return $this->error('End date cannot be prior to start date.');
        }
        $days = round(($end_sec - $start_sec) / 86400) + 1;

        // Check balances
        $balance = $this->leaveRepository->getLeaveBalance($emp['id']);
        if (!$balance) {
            return $this->error('Leave balance details not found for employee.');
        }

        // Reconcile type limits (excluding Unpaid)
        if (strcasecmp($leave_type, 'Unpaid') !== 0) {
            $balance_col = '';
            if (strcasecmp($leave_type, 'Casual') === 0) {
                $balance_col = 'casual_leaves';
            } elseif (strcasecmp($leave_type, 'Medical') === 0) {
                $balance_col = 'medical_leaves';
            } elseif (strcasecmp($leave_type, 'Earned') === 0) {
                $balance_col = 'earned_leaves';
            }

            if (empty($balance_col) || !isset($balance[$balance_col])) {
                return $this->error('Invalid leave type requested.');
            }

            if ($days > intval($balance[$balance_col])) {
                return $this->error("Insufficient leave balance. You requested $days days, but only have " . $balance[$balance_col] . " $leave_type leaves remaining.");
            }
        }

        $data = [
            'employee_id' => $emp['id'],
            'leave_type' => $leave_type,
            'start_date' => $start,
            'end_date' => $end,
            'reason' => $reason,
            'status' => 'Pending',
            'approved_by' => null,
            'comments' => ''
        ];

        $formats = ['%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s'];
        $inserted_id = $this->leaveRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to submit leave request.');
        }

        AuthService::logActivity($user_id, 'LEAVE_APPLY', "Applied for $days days of $leave_type leave starting $start");

        return $this->success('Leave application submitted successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * POST /leaves/:id/status (Approve/Reject)
     */
    public function approveReject(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $leave = $this->leaveRepository->findById($id);

        if (!$leave) {
            return $this->error('Leave application not found.', [], 404);
        }

        if ($leave['status'] !== 'Pending') {
            return $this->error('This leave request has already been processed (Current Status: ' . $leave['status'] . ').');
        }

        $params = $request->get_json_params();
        if (empty($params['status']) || !in_array($params['status'], ['Approved', 'Rejected'])) {
            return $this->error('status must be either Approved or Rejected.');
        }

        $status = sanitize_text_field($params['status']);
        $comments = sanitize_textarea_field($params['comments'] ?? '');
        $approver_id = get_current_user_id();

        global $wpdb;
        $wpdb->query('START TRANSACTION');

        $data = [
            'status' => $status,
            'approved_by' => $approver_id,
            'comments' => $comments
        ];
        $formats = ['%s', '%d', '%s'];

        $updated = $this->leaveRepository->update($id, $data, $formats);
        if (!$updated) {
            $wpdb->query('ROLLBACK');
            return $this->error('Failed to update leave request status.');
        }

        // If approved, deduct leave balance
        if ($status === 'Approved') {
            $start_sec = strtotime($leave['start_date']);
            $end_sec = strtotime($leave['end_date']);
            $days = round(($end_sec - $start_sec) / 86400) + 1;

            $deducted = $this->leaveRepository->deductLeaveBalance($leave['employee_id'], $leave['leave_type'], $days);
            if (!$deducted) {
                $wpdb->query('ROLLBACK');
                return $this->error('Failed to deduct leave days from employee balance.');
            }
        }

        $wpdb->query('COMMIT');

        AuthService::logActivity(
            $approver_id,
            'LEAVE_STATUS_CHANGE',
            "Leave ID $id status processed as $status (Approved days deducted if applicable)"
        );

        return $this->success("Leave request has been $status successfully.", $this->leaveRepository->findById($id));
    }

    /**
     * GET /leaves/balances
     */
    public function getBalances(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        $emp = $this->employeeRepository->findByUserId($user_id);
        if (!$emp) {
            return $this->error('Employee record not found.', [], 404);
        }

        $balance = $this->leaveRepository->getLeaveBalance($emp['id']);
        if (!$balance) {
            return $this->error('Leave balances not found for employee.');
        }

        return $this->success('Leave balances loaded.', $balance);
    }
}
