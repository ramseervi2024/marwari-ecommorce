<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\PayrollRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class PayrollController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new PayrollRepository();
    }

    /**
     * GET /payroll
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = [];
        if (!empty($params['teacher_id'])) {
            $filters['teacher_id'] = (int)$params['teacher_id'];
        }
        if (!empty($params['month'])) {
            $filters['month'] = (int)$params['month'];
        }
        if (!empty($params['year'])) {
            $filters['year'] = (int)$params['year'];
        }

        $result = $this->repository->findAll($params, ['id', 'teacher_id', 'month', 'year', 'net_salary', 'status'], [], $filters);
        return $this->success('Payroll records fetched successfully', $result);
    }

    /**
     * POST /payroll
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['teacher_id']) || empty($params['month']) || empty($params['year']) || !isset($params['salary_amount'])) {
            return $this->error('Validation failed: teacher_id, month, year, and salary_amount are required.');
        }

        $salary = (float)$params['salary_amount'];
        $allowance = isset($params['allowance']) ? (float)$params['allowance'] : 0.00;
        $deduction = isset($params['deduction']) ? (float)$params['deduction'] : 0.00;
        $net_salary = $salary + $allowance - $deduction;

        $data = [
            'teacher_id' => (int)$params['teacher_id'],
            'month' => (int)$params['month'],
            'year' => (int)$params['year'],
            'salary_amount' => $salary,
            'allowance' => $allowance,
            'deduction' => $deduction,
            'net_salary' => $net_salary,
            'status' => sanitize_text_field($params['status'] ?? 'PENDING'),
            'paid_date' => isset($params['paid_date']) ? sanitize_text_field($params['paid_date']) : null,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%d', '%d', '%d', '%f', '%f', '%f', '%f', '%s', '%s', '%s', '%s']);
        if (!$id) {
            return $this->error('Failed to create payroll log.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_PAYROLL', "Generated payroll entry for teacher ID: {$params['teacher_id']} (ID: $id)");
        return $this->success('Payroll generated successfully', array_merge(['id' => $id], $data), 201);
    }

    /**
     * PUT /payroll/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $payroll = $this->repository->findById($id);

        if (!$payroll) {
            return $this->error('Payroll record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $salary = isset($params['salary_amount']) ? (float)$params['salary_amount'] : (float)$payroll['salary_amount'];
        $allowance = isset($params['allowance']) ? (float)$params['allowance'] : (float)$payroll['allowance'];
        $deduction = isset($params['deduction']) ? (float)$params['deduction'] : (float)$payroll['deduction'];
        
        if (isset($params['salary_amount']) || isset($params['allowance']) || isset($params['deduction'])) {
            $data['salary_amount'] = $salary;
            $data['allowance'] = $allowance;
            $data['deduction'] = $deduction;
            $data['net_salary'] = $salary + $allowance - $deduction;
            $formats[] = '%f';
            $formats[] = '%f';
            $formats[] = '%f';
            $formats[] = '%f';
        }

        if (isset($params['status'])) {
            $data['status'] = sanitize_text_field($params['status']);
            $formats[] = '%s';
            if ($params['status'] === 'PAID') {
                $data['paid_date'] = current_time('Y-m-d');
                $formats[] = '%s';
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->repository->update($id, $data, $formats);
        
        // Return details with payslip HTML template
        $updated = $this->repository->findById($id);
        
        $payslip_html = "
        <div style='font-family: Arial, sans-serif; padding: 25px; border: 1px solid #ddd; max-width: 500px; margin: auto;'>
            <h3 style='text-align: center; color: #4F46E5;'>SALARY PAYSLIP</h3>
            <p style='text-align: center; font-size: 11px;'>Global International School</p>
            <hr>
            <p><strong>Employee ID:</strong> EMP-{$updated['teacher_id']}</p>
            <p><strong>Month / Year:</strong> {$updated['month']} / {$updated['year']}</p>
            <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                <tr>
                    <td style='padding: 5px; border-bottom: 1px solid #eee;'>Base Salary</td>
                    <td style='padding: 5px; text-align: right; border-bottom: 1px solid #eee;'>$" . number_format($updated['salary_amount'], 2) . "</td>
                </tr>
                <tr>
                    <td style='padding: 5px; border-bottom: 1px solid #eee;'>Allowance</td>
                    <td style='padding: 5px; text-align: right; border-bottom: 1px solid #eee;'>$" . number_format($updated['allowance'], 2) . "</td>
                </tr>
                <tr>
                    <td style='padding: 5px; border-bottom: 1px solid #eee; color: red;'>Deductions</td>
                    <td style='padding: 5px; text-align: right; border-bottom: 1px solid #eee; color: red;'>-$" . number_format($updated['deduction'], 2) . "</td>
                </tr>
                <tr style='font-weight: bold; background: #F9FAFB;'>
                    <td style='padding: 8px;'>Net Salary Paid</td>
                    <td style='padding: 8px; text-align: right;'>$" . number_format($updated['net_salary'], 2) . "</td>
                </tr>
            </table>
            <p style='margin-top: 20px; font-size: 12px;'><strong>Status:</strong> {$updated['status']}</p>
            <p style='font-size: 12px;'><strong>Paid Date:</strong> " . ($updated['paid_date'] ?: 'N/A') . "</p>
        </div>";

        return $this->success('Payroll updated successfully', [
            'record' => $updated,
            'payslip_base64_html' => base64_encode($payslip_html)
        ]);
    }

    /**
     * DELETE /payroll/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        if (!$this->repository->findById($id)) {
            return $this->error('Payroll record not found.', [], 404);
        }

        $this->repository->delete($id);
        return $this->success('Payroll record deleted successfully');
    }
}
