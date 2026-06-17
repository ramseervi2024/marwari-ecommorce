<?php
namespace HrManagementApi\Routes;

use HrManagementApi\Controllers\PayrollController;
use HrManagementApi\Middleware\AuthMiddleware;
use HrManagementApi\Middleware\RoleMiddleware;

class PayrollRoutes {
    public static function register() {
        $namespace  = 'hr-management/v1';
        $controller = new PayrollController();
        $auth       = [AuthMiddleware::class, 'authenticate'];
        $payroll_perm = function($request) {
            return AuthMiddleware::authenticate($request) && RoleMiddleware::requireCapability($request, 'manage_payroll');
        };

        // ─── SALARIES ─────────────────────────────────────────────────────────
        register_rest_route($namespace, '/payroll/salaries', [
            [
                'methods'             => 'GET',
                'callback'            => [$controller, 'getAllSalaries'],
                'permission_callback' => $payroll_perm,
            ],
            [
                'methods'             => 'POST',
                'callback'            => [$controller, 'upsertSalary'],
                'permission_callback' => $payroll_perm,
            ],
        ]);

        register_rest_route($namespace, '/payroll/salaries/(?P<employee_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [$controller, 'getSalaryByEmployee'],
            'permission_callback' => $auth,  // Employees can view own salary
        ]);

        // ─── PAYSLIPS ─────────────────────────────────────────────────────────
        register_rest_route($namespace, '/payroll/payslips', [
            'methods'             => 'GET',
            'callback'            => [$controller, 'getAllPayslips'],
            'permission_callback' => $auth,
        ]);

        register_rest_route($namespace, '/payroll/payslips/generate', [
            'methods'             => 'POST',
            'callback'            => [$controller, 'generatePayslip'],
            'permission_callback' => $payroll_perm,
        ]);

        register_rest_route($namespace, '/payroll/payslips/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [$controller, 'getPayslip'],
                'permission_callback' => $auth,
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [$controller, 'deletePayslip'],
                'permission_callback' => $payroll_perm,
            ],
        ]);

        register_rest_route($namespace, '/payroll/payslips/(?P<id>\d+)/mark-paid', [
            'methods'             => 'PUT',
            'callback'            => [$controller, 'markPayslipPaid'],
            'permission_callback' => $payroll_perm,
        ]);
    }
}
