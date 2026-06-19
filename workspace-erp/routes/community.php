<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\CommunityController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class CommunityRoutes {
    public static function register() {
        $controller = new CommunityController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/community/announcements', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexAnnouncements'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/community/announcements', [
            'methods' => 'POST',
            'callback' => [$controller, 'createAnnouncement'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_community')
        ]);
        register_rest_route($namespace, '/community/events', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexEvents'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/community/events', [
            'methods' => 'POST',
            'callback' => [$controller, 'createEvent'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_community')
        ]);
        register_rest_route($namespace, '/community/service-requests', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexServiceRequests'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
