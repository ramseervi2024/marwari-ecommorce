<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\CommissionController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class CommissionRoutes {
    
    public static function register() {
        $controller = new CommissionController();
        $namespace = 'real-estate-management/v1';

        // GET /commissions
        register_rest_route($namespace, '/commissions', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /commissions
        register_rest_route($namespace, '/commissions', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_commissions')
        ]);

        // PUT /commissions/:id
        register_rest_route($namespace, '/commissions/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_commissions')
        ]);

        // DELETE /commissions/:id
        register_rest_route($namespace, '/commissions/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_commissions')
        ]);
    }
}
