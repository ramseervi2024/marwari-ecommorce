<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\IpdController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class IpdRoutes {
    public static function register() {
        $controller = new IpdController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/ipd', [
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

        register_rest_route($namespace, '/ipd/(?P<id>\d+)', [
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
