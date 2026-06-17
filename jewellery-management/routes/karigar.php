<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\KarigarController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class KarigarRoutes {
    public static function register() {
        $controller = new KarigarController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/karigar', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/karigar', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_karigars')
        ]);

        register_rest_route($namespace, '/karigar/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/karigar/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_karigars')
        ]);

        register_rest_route($namespace, '/karigar/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_karigars')
        ]);
    }
}
