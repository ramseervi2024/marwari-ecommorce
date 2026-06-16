<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\PharmacyController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class PharmacyRoutes {
    public static function register() {
        $controller = new PharmacyController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/pharmacy', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_pharmacy')
            ]
        ]);

        register_rest_route($namespace, '/pharmacy/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_pharmacy')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_pharmacy')
            ]
        ]);
    }
}
