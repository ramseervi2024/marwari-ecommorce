<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\PropertyController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class PropertyRoutes {
    
    public static function register() {
        $controller = new PropertyController();
        $namespace = 'real-estate-management/v1';

        // GET /properties
        register_rest_route($namespace, '/properties', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /properties/:id
        register_rest_route($namespace, '/properties/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /properties
        register_rest_route($namespace, '/properties', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_properties')
        ]);

        // PUT /properties/:id
        register_rest_route($namespace, '/properties/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_properties')
        ]);

        // DELETE /properties/:id
        register_rest_route($namespace, '/properties/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_properties')
        ]);
    }
}
