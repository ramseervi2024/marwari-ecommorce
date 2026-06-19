<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\MediaController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class MediaRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new MediaController();
        register_rest_route($ns, '/media/upload', [
            'methods' => 'POST',
            'callback' => [$ctrl, 'upload'],
            'permission_callback' => [AuthMiddleware::class, 'authenticate']
        ]);
    }
}
