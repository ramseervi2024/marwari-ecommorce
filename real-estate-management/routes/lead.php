<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\LeadController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class LeadRoutes {
    
    public static function register() {
        $controller = new LeadController();
        $namespace = 'real-estate-management/v1';

        // GET /leads
        register_rest_route($namespace, '/leads', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /leads/:id
        register_rest_route($namespace, '/leads/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /leads
        register_rest_route($namespace, '/leads', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_leads')
        ]);

        // PUT /leads/:id
        register_rest_route($namespace, '/leads/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_leads')
        ]);

        // DELETE /leads/:id
        register_rest_route($namespace, '/leads/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_leads')
        ]);
    }
}
