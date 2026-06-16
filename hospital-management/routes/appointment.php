<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\AppointmentController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class AppointmentRoutes {
    public static function register() {
        $controller = new AppointmentController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/appointments', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_appointments')
            ]
        ]);

        register_rest_route($namespace, '/appointments/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_appointments')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_appointments')
            ]
        ]);
    }
}
