<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\SiteVisitController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class SiteVisitRoutes {
    
    public static function register() {
        $controller = new SiteVisitController();
        $namespace = 'real-estate-management/v1';

        // GET /site-visits
        register_rest_route($namespace, '/site-visits', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /site-visits
        register_rest_route($namespace, '/site-visits', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_site_visits')
        ]);

        // PUT /site-visits/:id
        register_rest_route($namespace, '/site-visits/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_site_visits')
        ]);

        // DELETE /site-visits/:id
        register_rest_route($namespace, '/site-visits/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_site_visits')
        ]);
    }
}
