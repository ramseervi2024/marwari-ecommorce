<?php
namespace ServiceManagementApi\Routes;

use ServiceManagementApi\Controllers\AmcController;
use ServiceManagementApi\Middleware\RoleMiddleware;

class AmcRoutes {
    
    public static function register() {
        $controller = new AmcController();
        $namespace = 'service-management/v1';

        // GET /amc
        register_rest_route($namespace, '/amc', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /amc/:id
        register_rest_route($namespace, '/amc/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /amc
        register_rest_route($namespace, '/amc', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_amc')
        ]);

        // PUT /amc/:id
        register_rest_route($namespace, '/amc/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_amc')
        ]);

        // DELETE /amc/:id
        register_rest_route($namespace, '/amc/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_amc')
        ]);
    }
}
