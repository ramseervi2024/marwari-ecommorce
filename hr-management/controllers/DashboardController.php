<?php
namespace HrManagementApi\Controllers;

use HrManagementApi\Repositories\EmployeeRepository;
use HrManagementApi\Repositories\AttendanceRepository;
use HrManagementApi\Repositories\LeaveRepository;
use HrManagementApi\Repositories\SalaryRepository;
use HrManagementApi\Repositories\PayslipRepository;
use WP_REST_Request;

class DashboardController extends BaseController {
    private $employeeRepository;
    private $attendanceRepository;
    private $leaveRepository;
    private $salaryRepository;
    private $payslipRepository;

    public function __construct() {
        $this->employeeRepository   = new EmployeeRepository();
        $this->attendanceRepository = new AttendanceRepository();
        $this->leaveRepository      = new LeaveRepository();
        $this->salaryRepository     = new SalaryRepository();
        $this->payslipRepository    = new PayslipRepository();
    }

    /**
     * GET /dashboard/stats
     * Admin/Manager: company-wide stats
     * Employee: own stats
     */
    public function getStats(WP_REST_Request $request) {
        global $wpdb;
        $current_user = wp_get_current_user();
        $today        = current_time('Y-m-d');
        $this_month   = date('F', strtotime($today));
        $this_year    = intval(date('Y', strtotime($today)));

        // Company-wide admin stats
        if ($current_user->has_cap('manage_employees')) {
            $table_employees  = $wpdb->prefix . 'hr_employees';
            $table_attendance = $wpdb->prefix . 'hr_attendance';
            $table_leaves     = $wpdb->prefix . 'hr_leaves';
            $table_payslips   = $wpdb->prefix . 'hr_payslips';
            $table_salaries   = $wpdb->prefix . 'hr_salaries';

            $total_employees   = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_employees WHERE status = 'ACTIVE' AND deleted_at IS NULL");
            $present_today     = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_attendance WHERE date = %s AND status IN ('Present', 'Late')", $today));
            $absent_today      = $total_employees - $present_today;
            $late_today        = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_attendance WHERE date = %s AND status = 'Late'", $today));
            $halfday_today     = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_attendance WHERE date = %s AND status = 'Half Day'", $today));
            $pending_leaves    = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_leaves WHERE status = 'Pending'");
            $approved_leaves   = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_leaves WHERE status = 'Approved'");

            // Monthly gross payout
            $monthly_gross = (float)$wpdb->get_var($wpdb->prepare(
                "SELECT SUM(net_salary) FROM $table_payslips WHERE month = %s AND year = %d",
                $this_month, $this_year
            ));

            // Total payable from salary profiles
            $total_payable = (float)$wpdb->get_var("SELECT SUM(net_salary) FROM $table_salaries WHERE status = 'Active'");

            // Recent activity: 5 last attendance records
            $recent_checkins = $wpdb->get_results($wpdb->prepare(
                "SELECT a.*, e.department FROM $table_attendance a 
                 JOIN $table_employees e ON a.employee_id = e.id 
                 WHERE a.date = %s ORDER BY a.created_at DESC LIMIT 5",
                $today
            ), ARRAY_A);

            foreach ($recent_checkins as &$row) {
                $emp  = $this->employeeRepository->findById($row['employee_id']);
                $user = $emp ? get_userdata($emp['user_id']) : null;
                $row['employee_name'] = $user ? $user->display_name : 'Unknown';
            }

            // Pending leave requests
            $pending_leave_requests = $wpdb->get_results(
                "SELECT l.*, e.department FROM $table_leaves l 
                 JOIN $table_employees e ON l.employee_id = e.id 
                 WHERE l.status = 'Pending' ORDER BY l.created_at DESC LIMIT 5",
                ARRAY_A
            );

            foreach ($pending_leave_requests as &$row) {
                $emp  = $this->employeeRepository->findById($row['employee_id']);
                $user = $emp ? get_userdata($emp['user_id']) : null;
                $row['employee_name'] = $user ? $user->display_name : 'Unknown';
            }

            return $this->success('Dashboard stats retrieved.', [
                'summary' => [
                    'total_employees'   => $total_employees,
                    'present_today'     => $present_today,
                    'absent_today'      => $absent_today,
                    'late_today'        => $late_today,
                    'halfday_today'     => $halfday_today,
                    'pending_leaves'    => $pending_leaves,
                    'approved_leaves'   => $approved_leaves,
                    'monthly_gross_payout' => round($monthly_gross, 2),
                    'total_payable'     => round($total_payable, 2),
                    'current_month'     => $this_month . ' ' . $this_year,
                ],
                'recent_checkins'       => $recent_checkins,
                'pending_leave_requests' => $pending_leave_requests,
            ]);
        }

        // ─── EMPLOYEE OWN VIEW ─────────────────────────────────────────────
        $emp = $this->employeeRepository->findByUserId($current_user->ID);
        if (!$emp) {
            return $this->error('No employee profile linked to your account.', [], 404);
        }

        $emp_id = $emp['id'];

        // Today's attendance
        $table_attendance = $wpdb->prefix . 'hr_attendance';
        $today_record = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_attendance WHERE employee_id = %d AND date = %s",
            $emp_id, $today
        ), ARRAY_A);

        // Leave balance
        $leave_balance = $this->leaveRepository->getLeaveBalance($emp_id);

        // Salary
        $salary = $this->salaryRepository->findByEmployeeId($emp_id);

        // Last 3 payslips
        $table_payslips = $wpdb->prefix . 'hr_payslips';
        $recent_payslips = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_payslips WHERE employee_id = %d ORDER BY year DESC, generated_at DESC LIMIT 3",
            $emp_id
        ), ARRAY_A);

        // Pending leave count this month
        $table_leaves = $wpdb->prefix . 'hr_leaves';
        $pending_leaves_own = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_leaves WHERE employee_id = %d AND status = 'Pending'",
            $emp_id
        ));

        // Days present this month
        $month_start    = date('Y-m-01', strtotime($today));
        $days_present   = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_attendance WHERE employee_id = %d AND date >= %s AND date <= %s AND status IN ('Present', 'Late', 'Half Day')",
            $emp_id, $month_start, $today
        ));

        return $this->success('Employee dashboard stats retrieved.', [
            'employee'       => [
                'id'          => $emp['id'],
                'department'  => $emp['department'],
                'designation' => $emp['designation'],
                'status'      => $emp['status'],
                'date_of_joining' => $emp['date_of_joining'],
            ],
            'today_attendance'  => $today_record,
            'leave_balances'    => $leave_balance ?: [],
            'pending_leaves'    => $pending_leaves_own,
            'days_present_this_month' => $days_present,
            'salary'            => $salary ? [
                'base_salary'  => $salary['base_salary'],
                'net_salary'   => $salary['net_salary'],
                'pf'           => $salary['pf_contribution'],
                'esi'          => $salary['esi_contribution'],
            ] : null,
            'recent_payslips'   => $recent_payslips,
        ]);
    }

    /**
     * GET /dashboard/activity-logs
     * Admin only
     */
    public function getActivityLogs(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_params();
        $limit  = max(1, min(100, intval($params['limit'] ?? 50)));
        $page   = max(1, intval($params['page'] ?? 1));
        $offset = ($page - 1) * $limit;
        $table  = $wpdb->prefix . 'hr_activity_logs';

        $total  = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table");
        $logs   = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $limit, $offset
        ), ARRAY_A);

        foreach ($logs as &$log) {
            if ($log['user_id']) {
                $user = get_userdata($log['user_id']);
                $log['username'] = $user ? $user->display_name : 'System';
            } else {
                $log['username'] = 'System';
            }
        }

        return $this->success('Activity logs retrieved.', [
            'total'  => $total,
            'page'   => $page,
            'limit'  => $limit,
            'pages'  => ceil($total / $limit),
            'data'   => $logs,
        ]);
    }
}
