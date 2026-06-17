<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\MediaController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class MediaRoutes {
    
    public static function register() {
        $controller = new MediaController();
        $namespace = 'construction-management/v1';

        // POST /media/upload
        register_rest_route($namespace, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$controller, 'upload'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
