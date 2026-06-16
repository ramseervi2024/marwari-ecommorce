<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\MediaController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class MediaRoutes {
    public static function register() {
        $controller = new MediaController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$controller, 'upload'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
