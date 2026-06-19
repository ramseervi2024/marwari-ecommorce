<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\HrController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class HrRoutes {
    public static function register() {
        $controller = new HrController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/hr/employees', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexEmployees'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_hr')
        ]);
        register_rest_route($namespace, '/hr/employees', [
            'methods' => 'POST',
            'callback' => [$controller, 'createEmployee'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_hr')
        ]);
        register_rest_route($namespace, '/hr/attendance', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexAttendance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_hr')
        ]);
    }
}
