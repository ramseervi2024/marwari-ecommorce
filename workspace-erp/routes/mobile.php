<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\MobileController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class MobileRoutes {
    public static function register() {
        $controller = new MobileController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/mobile/dashboard', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDashboard'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/bookings', [
            'methods' => 'GET',
            'callback' => [$controller, 'getBookings'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/visitors', [
            'methods' => 'GET',
            'callback' => [$controller, 'getVisitors'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/invoices', [
            'methods' => 'GET',
            'callback' => [$controller, 'getInvoices'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/service-request', [
            'methods' => 'POST',
            'callback' => [$controller, 'createServiceRequest'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/meeting-room-booking', [
            'methods' => 'POST',
            'callback' => [$controller, 'createMeetingRoomBooking'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
