<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\DoctorController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class DoctorRoutes {
    public static function register() {
        $controller = new DoctorController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/doctors', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_doctors')
            ]
        ]);

        register_rest_route($namespace, '/doctors/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_doctors')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_doctors')
            ]
        ]);
    }
}
