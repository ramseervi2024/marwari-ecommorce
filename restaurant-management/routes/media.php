<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\MediaController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class MediaRoutes {
    public static function register() {
        $controller = new MediaController();
        $namespace = 'restaurant-management/v1';

        register_rest_route($namespace, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$controller, 'upload'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
