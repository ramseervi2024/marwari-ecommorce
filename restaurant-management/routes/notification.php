<?php
namespace RestaurantManagementApi\Routes;

use RestaurantManagementApi\Controllers\NotificationController;
use RestaurantManagementApi\Middleware\RoleMiddleware;

class NotificationRoutes {
    public static function register() {
        $controller = new NotificationController();
        $namespace = 'restaurant-management/v1';

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
        register_rest_route($namespace, '/notifications/push', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendPush'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
    }
}
