<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\LaboratoryController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class LaboratoryRoutes {
    public static function register() {
        $controller = new LaboratoryController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/laboratory', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAll'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'create'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_laboratory')
            ]
        ]);

        register_rest_route($namespace, '/laboratory/(?P<id>\d+)', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getById'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ],
            [
                'methods' => 'PUT',
                'callback' => [$controller, 'update'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_laboratory')
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$controller, 'delete'],
                'permission_callback' => RoleMiddleware::hasCapability('manage_laboratory')
            ]
        ]);

        // Specific sub-route for lab tests catalog
        register_rest_route($namespace, '/lab/tests', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getTestsCatalog'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ]
        ]);
    }
}
