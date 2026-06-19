<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\WorkspaceController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class WorkspaceRoutes {
    public static function register() {
        $controller = new WorkspaceController();
        $namespace = 'workspace-erp/v1';

        // Buildings
        register_rest_route($namespace, '/workspaces/buildings', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexBuildings'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/workspaces/buildings', [
            'methods' => 'POST',
            'callback' => [$controller, 'createBuilding'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_workspaces')
        ]);

        // Floors
        register_rest_route($namespace, '/workspaces/floors', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexFloors'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // Workspaces
        register_rest_route($namespace, '/workspaces', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexWorkspaces'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/workspaces', [
            'methods' => 'POST',
            'callback' => [$controller, 'createWorkspace'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_workspaces')
        ]);

        // Seats
        register_rest_route($namespace, '/workspaces/seats', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexSeats'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // Meeting Rooms
        register_rest_route($namespace, '/workspaces/meeting-rooms', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexMeetingRooms'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // Bookings
        register_rest_route($namespace, '/workspaces/bookings', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexBookings'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/workspaces/bookings', [
            'methods' => 'POST',
            'callback' => [$controller, 'createBooking'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/workspaces/bookings/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateBooking'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/workspaces/bookings/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteBooking'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/workspaces/buildings/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateBuilding'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_workspaces')
        ]);
        register_rest_route($namespace, '/workspaces/buildings/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteBuilding'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_workspaces')
        ]);
    }
}
