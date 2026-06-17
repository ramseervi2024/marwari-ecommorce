<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\MediaController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class MediaRoutes {
    public static function register() {
        $controller = new MediaController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$controller, 'upload'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
