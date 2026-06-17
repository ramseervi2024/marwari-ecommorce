<?php
namespace RealEstateManagementApi\Routes;

use RealEstateManagementApi\Controllers\PipelineController;
use RealEstateManagementApi\Middleware\RoleMiddleware;

class PipelineRoutes {
    
    public static function register() {
        $controller = new PipelineController();
        $namespace = 'real-estate-management/v1';

        // GET /pipeline
        register_rest_route($namespace, '/pipeline', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /pipeline
        register_rest_route($namespace, '/pipeline', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_leads')
        ]);

        // PUT /pipeline/:id
        register_rest_route($namespace, '/pipeline/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_leads')
        ]);
    }
}
