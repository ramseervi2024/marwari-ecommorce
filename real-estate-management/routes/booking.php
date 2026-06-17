<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\BookingController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class BookingRoutes {
    
    public static function register() {
        $controller = new BookingController();
        $namespace = 'real-estate-management/v1';

        // GET /bookings
        register_rest_route($namespace, '/bookings', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /bookings/:id
        register_rest_route($namespace, '/bookings/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /bookings
        register_rest_route($namespace, '/bookings', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_bookings')
        ]);

        // PUT /bookings/:id
        register_rest_route($namespace, '/bookings/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_bookings')
        ]);

        // DELETE /bookings/:id
        register_rest_route($namespace, '/bookings/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_bookings')
        ]);
    }
}
