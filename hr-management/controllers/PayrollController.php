<?php
namespace HrManagementApi\Controllers;

use HrManagementApi\Repositories\EmployeeRepository;
use HrManagementApi\Repositories\SalaryRepository;
use HrManagementApi\Repositories\PayslipRepository;
use HrManagementApi\Services\AuthService;
use WP_REST_Request;

class PayrollController extends BaseController {
    private $employeeRepository;
    private $salaryRepository;
    private $payslipRepository;

    public function __construct() {
        $this->employeeRepository = new EmployeeRepository();
        $this->salaryRepository   = new SalaryRepository();
        $this->payslipRepository  = new PayslipRepository();
    }

    // ─── SALARY STRUCTURE ────────────────────────────────────────────────────

    /**
     * GET /payroll/salaries
     * List all salary structures (Admin / Accountant)
     */
    public function getAllSalaries(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'employee_id', 'base_salary', 'net_salary', 'status'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->salaryRepository->findAll($params, $allowed_sorts, [], $extra_filters);

        foreach ($results['data'] as &$row) {
            $emp = $this->employeeRepository->findById($row['employee_id']);
            $user = $emp ? get_userdata($emp['user_id']) : null;
            $row['employee_name'] = $user ? $user->display_name : 'Unknown';
            $row['department']    = $emp['department'] ?? '';
        }

        return $this->success('Salary structures retrieved.', $results);
    }

    /**
     * GET /payroll/salaries/:employee_id
     * Get a specific employee's salary profile
     */
    public function getSalaryByEmployee(WP_REST_Request $request) {
        $employee_id = intval($request->get_param('employee_id'));
        $emp = $this->employeeRepository->findById($employee_id);
        if (!$emp) {
            return $this->error('Employee not found.', [], 404);
        }

        $salary = $this->salaryRepository->findByEmployeeId($employee_id);
        if (!$salary) {
            return $this->error('Salary profile not found for this employee.', [], 404);
        }

        $user = get_userdata($emp['user_id']);
        $salary['employee_name'] = $user ? $user->display_name : 'Unknown';
        $salary['department']    = $emp['department'];
        $salary['designation']   = $emp['designation'];

        return $this->success('Salary profile retrieved.', $salary);
    }

    /**
     * POST /payroll/salaries
     * Create or update salary structure for an employee (Admin / Accountant)
     */
    public function upsertSalary(WP_REST_Request $request) {
        $params = $request->get_json_params();

        $employee_id = intval($params['employee_id'] ?? 0);
        if (!$employee_id) {
            return $this->error('employee_id is required.');
        }

        $emp = $this->employeeRepository->findById($employee_id);
        if (!$emp) {
            return $this->error('Employee not found.', [], 404);
        }

        $base_salary  = floatval($params['base_salary']  ?? 0);
        $allowances   = floatval($params['allowances']   ?? 0);
        $deductions   = floatval($params['deductions']   ?? 0);

        // PF = 12% of basic, ESI = 0.75% of gross
        $gross        = $base_salary + $allowances;
        $pf           = isset($params['pf_contribution'])  ? floatval($params['pf_contribution'])  : round($base_salary * 0.12, 2);
        $esi          = isset($params['esi_contribution']) ? floatval($params['esi_contribution']) : round($gross * 0.0075, 2);
        $net_salary   = $gross - ($deductions + $pf + $esi);

        $data = [
            'employee_id'    => $employee_id,
            'base_salary'    => $base_salary,
            'allowances'     => $allowances,
            'deductions'     => $deductions,
            'pf_contribution'  => $pf,
            'esi_contribution' => $esi,
            'net_salary'     => $net_salary,
            'status'         => sanitize_text_field($params['status'] ?? 'Active'),
        ];
        $formats = ['%d', '%f', '%f', '%f', '%f', '%f', '%f', '%s'];

        // Check if salary record already exists
        $existing = $this->salaryRepository->findByEmployeeId($employee_id);
        if ($existing) {
            unset($data['employee_id']);
            array_shift($formats);
            $this->salaryRepository->update($existing['id'], $data, $formats);
            AuthService::logActivity(get_current_user_id(), 'SALARY_UPDATE', "Updated salary for employee_id: $employee_id");
            return $this->success('Salary structure updated.', $this->salaryRepository->findByEmployeeId($employee_id));
        }

        $id = $this->salaryRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to create salary structure.');
        }

        AuthService::logActivity(get_current_user_id(), 'SALARY_CREATE', "Created salary for employee_id: $employee_id");
        return $this->success('Salary structure created.', $this->salaryRepository->findById($id), 201);
    }

    // ─── PAYSLIPS ────────────────────────────────────────────────────────────

    /**
     * GET /payroll/payslips
     * List payslips (Admin / Accountant sees all; Employee sees own)
     */
    public function getAllPayslips(WP_REST_Request $request) {
        $params      = $request->get_params();
        $current_user = wp_get_current_user();

        $allowed_sorts  = ['id', 'employee_id', 'month', 'year', 'net_salary', 'status', 'generated_at'];
        $extra_filters  = [];

        // Role restriction: employees only see their own
        if (!$current_user->has_cap('manage_payroll')) {
            $emp = $this->employeeRepository->findByUserId($current_user->ID);
            if (!$emp) {
                return $this->error('No employee profile linked to your account.', [], 404);
            }
            $extra_filters['employee_id'] = $emp['id'];
        } elseif (isset($params['employee_id'])) {
            $extra_filters['employee_id'] = intval($params['employee_id']);
        }

        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['month'])) {
            $extra_filters['month'] = sanitize_text_field($params['month']);
        }
        if (isset($params['year'])) {
            $extra_filters['year'] = intval($params['year']);
        }

        $results = $this->payslipRepository->findAll($params, $allowed_sorts, [], $extra_filters);

        foreach ($results['data'] as &$row) {
            $emp  = $this->employeeRepository->findById($row['employee_id']);
            $user = $emp ? get_userdata($emp['user_id']) : null;
            $row['employee_name'] = $user ? $user->display_name : 'Unknown';
        }

        return $this->success('Payslips retrieved.', $results);
    }

    /**
     * GET /payroll/payslips/:id
     */
    public function getPayslip(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $payslip = $this->payslipRepository->findById($id);
        if (!$payslip) {
            return $this->error('Payslip not found.', [], 404);
        }

        // Role check: employee can only see own
        $current_user = wp_get_current_user();
        if (!$current_user->has_cap('manage_payroll')) {
            $emp = $this->employeeRepository->findByUserId($current_user->ID);
            if (!$emp || $emp['id'] !== (int)$payslip['employee_id']) {
                return $this->error('Access denied.', [], 403);
            }
        }

        $emp  = $this->employeeRepository->findById($payslip['employee_id']);
        $user = $emp ? get_userdata($emp['user_id']) : null;
        $payslip['employee_name'] = $user ? $user->display_name : 'Unknown';
        $payslip['department']    = $emp['department']  ?? '';
        $payslip['designation']   = $emp['designation'] ?? '';
        $payslip['pf_number']     = $emp['pf_number']   ?? '';
        $payslip['esi_number']    = $emp['esi_number']  ?? '';

        return $this->success('Payslip retrieved.', $payslip);
    }

    /**
     * POST /payroll/payslips/generate
     * Generate payslip for an employee for a given month/year (Admin / Accountant)
     */
    public function generatePayslip(WP_REST_Request $request) {
        $params      = $request->get_json_params();
        $employee_id = intval($params['employee_id'] ?? 0);
        $month       = sanitize_text_field($params['month']  ?? '');
        $year        = intval($params['year'] ?? 0);

        if (!$employee_id || !$month || !$year) {
            return $this->error('employee_id, month, and year are required.');
        }

        $emp = $this->employeeRepository->findById($employee_id);
        if (!$emp) {
            return $this->error('Employee not found.', [], 404);
        }

        // Check duplicate
        $existing = $this->payslipRepository->findByEmployeeIdMonthAndYear($employee_id, $month, $year);
        if ($existing) {
            return $this->error("Payslip for $month $year already exists for this employee.", [], 409);
        }

        // Pull salary profile
        $salary = $this->salaryRepository->findByEmployeeId($employee_id);
        if (!$salary) {
            return $this->error('No salary structure found. Please configure salary first.', [], 404);
        }

        // Allow overriding PF/ESI contribution for this month
        $base_salary = floatval($params['base_salary']  ?? $salary['base_salary']);
        $allowances  = floatval($params['allowances']   ?? $salary['allowances']);
        $deductions  = floatval($params['deductions']   ?? $salary['deductions']);
        $pf          = floatval($params['pf_deduction'] ?? $salary['pf_contribution']);
        $esi         = floatval($params['esi_deduction'] ?? $salary['esi_contribution']);
        $gross       = $base_salary + $allowances;
        $net_salary  = $gross - ($deductions + $pf + $esi);

        $data = [
            'employee_id'   => $employee_id,
            'month'         => $month,
            'year'          => $year,
            'base_salary'   => $base_salary,
            'allowances'    => $allowances,
            'deductions'    => $deductions,
            'pf_deduction'  => $pf,
            'esi_deduction' => $esi,
            'net_salary'    => $net_salary,
            'status'        => 'Generated',
        ];
        $formats = ['%d', '%s', '%d', '%f', '%f', '%f', '%f', '%f', '%f', '%s'];

        $id = $this->payslipRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to generate payslip.');
        }

        AuthService::logActivity(get_current_user_id(), 'PAYSLIP_GENERATE', "Generated payslip for employee_id: $employee_id | $month $year");
        return $this->success('Payslip generated successfully.', $this->payslipRepository->findById($id), 201);
    }

    /**
     * PUT /payroll/payslips/:id/mark-paid
     * Mark a payslip as Paid (Admin / Accountant)
     */
    public function markPayslipPaid(WP_REST_Request $request) {
        $id      = intval($request->get_param('id'));
        $payslip = $this->payslipRepository->findById($id);
        if (!$payslip) {
            return $this->error('Payslip not found.', [], 404);
        }

        if ($payslip['status'] === 'Paid') {
            return $this->error('Payslip is already marked as Paid.');
        }

        $this->payslipRepository->update($id, ['status' => 'Paid'], ['%s']);
        AuthService::logActivity(get_current_user_id(), 'PAYSLIP_PAID', "Marked payslip ID: $id as Paid.");
        return $this->success('Payslip marked as Paid.', $this->payslipRepository->findById($id));
    }

    /**
     * DELETE /payroll/payslips/:id
     * Delete a draft payslip (Admin only)
     */
    public function deletePayslip(WP_REST_Request $request) {
        $id      = intval($request->get_param('id'));
        $payslip = $this->payslipRepository->findById($id);
        if (!$payslip) {
            return $this->error('Payslip not found.', [], 404);
        }

        if ($payslip['status'] === 'Paid') {
            return $this->error('Cannot delete a paid payslip.');
        }

        $this->payslipRepository->delete($id);
        AuthService::logActivity(get_current_user_id(), 'PAYSLIP_DELETE', "Deleted payslip ID: $id.");
        return $this->success('Payslip deleted.');
    }
}
