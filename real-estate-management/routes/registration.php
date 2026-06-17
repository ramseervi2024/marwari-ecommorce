<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\RegistrationController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class RegistrationRoutes {
    
    public static function register() {
        $controller = new RegistrationController();
        $namespace = 'real-estate-management/v1';

        // GET /registrations
        register_rest_route($namespace, '/registrations', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /registrations
        register_rest_route($namespace, '/registrations', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_bookings')
        ]);

        // PUT /registrations/:id
        register_rest_route($namespace, '/registrations/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_bookings')
        ]);
    }
}
