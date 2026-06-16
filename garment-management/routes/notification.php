<?php
namespace GarmentManagementApi\Routes;

use GarmentManagementApi\Controllers\NotificationController;
use GarmentManagementApi\Middleware\RoleMiddleware;

class NotificationRoutes {
    public static function register() {
        $controller = new NotificationController();
        $namespace = 'garment-management/v1';

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
