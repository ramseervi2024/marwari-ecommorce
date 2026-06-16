<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\DashboardController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class DashboardRoutes {
    public static function register() {
        $controller = new DashboardController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/dashboard', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getStats'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ]
        ]);
    }
}
