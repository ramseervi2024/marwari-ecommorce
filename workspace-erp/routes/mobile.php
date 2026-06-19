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
        register_rest_route($namespace, '/mobile/invoices/(?P<id>\d+)/pay', [
            'methods' => 'POST',
            'callback' => [$controller, 'payInvoice'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/service-request', [
            'methods' => 'POST',
            'callback' => [$controller, 'createServiceRequest'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/service-requests', [
            'methods' => 'GET',
            'callback' => [$controller, 'getServiceRequests'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/meeting-room-booking', [
            'methods' => 'POST',
            'callback' => [$controller, 'createMeetingRoomBooking'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/visitors', [
            'methods' => 'POST',
            'callback' => [$controller, 'createVisitor'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/visitors/(?P<id>\d+)/approve', [
            'methods' => 'POST',
            'callback' => [$controller, 'approveVisitor'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/announcements', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAnnouncements'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/events', [
            'methods' => 'GET',
            'callback' => [$controller, 'getEvents'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/mobile/meeting-rooms', [
            'methods' => 'GET',
            'callback' => [$controller, 'getMeetingRooms'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
