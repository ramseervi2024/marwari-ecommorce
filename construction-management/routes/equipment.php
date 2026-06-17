<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\EquipmentController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class EquipmentRoutes {
    
    public static function register() {
        $controller = new EquipmentController();
        $namespace = 'construction-management/v1';

        // GET /equipment
        register_rest_route($namespace, '/equipment', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /equipment/:id
        register_rest_route($namespace, '/equipment/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /equipment
        register_rest_route($namespace, '/equipment', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_equipment')
        ]);

        // PUT /equipment/:id
        register_rest_route($namespace, '/equipment/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_equipment')
        ]);

        // DELETE /equipment/:id
        register_rest_route($namespace, '/equipment/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_equipment')
        ]);
    }
}
