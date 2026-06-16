<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\OpdController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class OpdRoutes {
    public static function register() {
        $controller = new OpdController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/opd', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_opd_ipd')
            ]
        ]);

        register_rest_route($namespace, '/opd/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_opd_ipd')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_opd_ipd')
            ]
        ]);
    }
}
