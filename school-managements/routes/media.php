<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\MediaController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class MediaRoutes {
    
    public static function register() {
        $controller = new MediaController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$controller, 'upload'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
