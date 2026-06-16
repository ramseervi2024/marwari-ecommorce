<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\MachineController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class MachineRoutes {
    public static function register() {
        $controller = new MachineController();
        $namespace = 'garment-management/v1';

        register_rest_route($namespace, '/machine', [
            'methods' => 'GET',
            'callback' => [$controller, 'index'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/machine', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/machine/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'get'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/machine/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);

        register_rest_route($namespace, '/machine/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_garment_production')
        ]);
    }
}
