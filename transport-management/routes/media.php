<?php
namespace TransportManagementApi\Routes;

if (!defined('ABSPATH')) {
    exit;
}

use TransportManagementApi\Controllers\MediaController;
use TransportManagementApi\Middleware\RoleMiddleware;

class MediaRoutes {
    
    public static function register() {
        $controller = new MediaController();
        $namespace = 'transport-management/v1';

        register_rest_route($namespace, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$controller, 'upload'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
