<?php
namespace HospitalManagementApi\Routes;

use HospitalManagementApi\Controllers\MediaController;
use HospitalManagementApi\Middleware\RoleMiddleware;

class MediaRoutes {
    public static function register() {
        $controller = new MediaController();
        $namespace = 'hospital-management/v1';

        register_rest_route($namespace, '/media/upload', [
            [
                'methods' => 'POST',
                'callback' => [$controller, 'upload'],
                'permission_callback' => RoleMiddleware::hasCapability('read')
            ]
        ]);
    }
}
