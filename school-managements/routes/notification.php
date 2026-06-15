<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\NotificationController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class NotificationRoutes {
    
    public static function register() {
        $controller = new NotificationController();
        $namespace = 'school-management/v1';

        register_rest_route($namespace, '/notifications/email', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendEmail'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);

        register_rest_route($namespace, '/notifications/sms', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendSMS'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);

        register_rest_route($namespace, '/notifications/push', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendPushNotification'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);

        register_rest_route($namespace, '/notifications/whatsapp', [
            'methods' => 'POST',
            'callback' => [$controller, 'sendWhatsApp'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_school')
        ]);
    }
}
