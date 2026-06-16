<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\PrescriptionController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class PrescriptionRoutes {
    public static function register() {
        $controller = new PrescriptionController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/prescriptions', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('write_prescriptions')
            ]
        ]);

        register_rest_route($namespace, '/prescriptions/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('write_prescriptions')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('write_prescriptions')
            ]
        ]);
    }
}
