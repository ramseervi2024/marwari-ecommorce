<?php
namespace JewelleryManagementApi\Routes;

use JewelleryManagementApi\Controllers\NotificationController;
use JewelleryManagementApi\Middleware\RoleMiddleware;

class NotificationRoutes {
    public static function register() {
        $controller = new NotificationController();
        $namespace = 'jewellery-management/v1';

        register_rest_route($namespace, '/notification/email', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendEmail'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/notification/sms', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendSms'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        register_rest_route($namespace, '/notification/whatsapp', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendWhatsapp'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
