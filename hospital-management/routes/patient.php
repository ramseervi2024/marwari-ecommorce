<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\PatientController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class PatientRoutes {
    public static function register() {
        $controller = new PatientController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/patients', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_patients')
            ]
        ]);

        register_rest_route($namespace, '/patients/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_patients')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_patients')
            ]
        ]);
    }
}
