<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\JobWorkController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class JobWorkRoutes {
    public static function register() {
        $controller = new JobWorkController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/job-work', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/job-work', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_karigars')
        ]);

        register_rest_route($namespace, '/job-work/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/job-work/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_karigars')
        ]);

        register_rest_route($namespace, '/job-work/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_jewel_karigars')
        ]);
    }
}
