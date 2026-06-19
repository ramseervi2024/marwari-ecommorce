<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\FacilityController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class FacilityRoutes {
    public static function register() {
        $controller = new FacilityController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/facility/tickets', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexTickets'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/facility/tickets', [
            'methods' => 'POST',
            'callback' => [$controller, 'createTicket'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/facility/tickets/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateTicket'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_facilities')
        ]);
        register_rest_route($namespace, '/facility/tickets/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteTicket'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_facilities')
        ]);

        register_rest_route($namespace, '/facility/work-orders', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexWorkOrders'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_facilities')
        ]);

        register_rest_route($namespace, '/facility/maintenance', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexMaintenance'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_facilities')
        ]);
    }
}
