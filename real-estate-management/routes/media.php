<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\MediaController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class MediaRoutes {
    
    public static function register() {
        $controller = new MediaController();
        $namespace = 'real-estate-management/v1';

        // POST /media/upload
        register_rest_route($namespace, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$controller, 'upload'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
