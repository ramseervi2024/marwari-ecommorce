<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\BrokerController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class BrokerRoutes {
    
    public static function register() {
        $controller = new BrokerController();
        $namespace = 'real-estate-management/v1';

        // GET /brokers
        register_rest_route($namespace, '/brokers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /brokers/:id
        register_rest_route($namespace, '/brokers/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /brokers
        register_rest_route($namespace, '/brokers', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_brokers')
        ]);

        // PUT /brokers/:id
        register_rest_route($namespace, '/brokers/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_brokers')
        ]);

        // DELETE /brokers/:id
        register_rest_route($namespace, '/brokers/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_brokers')
        ]);
    }
}
