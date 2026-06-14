<?php
namespace FleetTrackPro\Routes;

use FleetTrackPro\Controllers\MediaController;
use FleetTrackPro\Middleware\RoleMiddleware;

class MediaRoutes {
    
    public static function register() {
        $controller = new MediaController();
        $namespace = 'fleet-track/v1';

        register_rest_route($namespace, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$controller, 'upload'],
            'permission_callback' => RoleMiddleware::hasCapability('upload_documents')
        ]);
    }
}
