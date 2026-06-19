<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\SmartBuildingController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class SmartBuildingRoutes {
    public static function register() {
        $controller = new SmartBuildingController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/smartbuilding/devices', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexDevices'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_smartbuilding')
        ]);
        register_rest_route($namespace, '/smartbuilding/devices', [
            'methods' => 'POST',
            'callback' => [$controller, 'createDevice'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_smartbuilding')
        ]);
        register_rest_route($namespace, '/smartbuilding/devices/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateDevice'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_smartbuilding')
        ]);
        register_rest_route($namespace, '/smartbuilding/devices/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteDevice'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_smartbuilding')
        ]);
        register_rest_route($namespace, '/smartbuilding/sensors', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexSensors'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_smartbuilding')
        ]);
        register_rest_route($namespace, '/smartbuilding/access-logs', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexAccessLogs'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_smartbuilding')
        ]);
    }
}
