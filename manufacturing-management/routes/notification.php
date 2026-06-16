<?php
namespace ManufacturingManagementApi\Routes;

use ManufacturingManagementApi\Controllers\NotificationController;
use ManufacturingManagementApi\Middleware\RoleMiddleware;

class NotificationRoutes {
    public static function register() {
        $controller = new NotificationController();
        $namespace = 'manufacturing-management/v1';

        register_rest_route($namespace, '/notifications/email', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendEmail'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/notifications/sms', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendSms'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/notifications/whatsapp', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendWhatsapp'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
