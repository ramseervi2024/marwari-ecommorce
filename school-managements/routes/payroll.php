<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\PayrollController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class PayrollRoutes {
    
    public static function register() {
        $controller = new PayrollController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/payroll', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payroll')
        ]);

        register_rest_route($namespace, '/payroll', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payroll')
        ]);

        register_rest_route($namespace, '/payroll/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payroll')
        ]);

        register_rest_route($namespace, '/payroll/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_payroll')
        ]);
    }
}
