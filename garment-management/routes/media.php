<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\MediaController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class MediaRoutes {
    public static function register() {
        $controller = new MediaController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$controller, 'upload'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
