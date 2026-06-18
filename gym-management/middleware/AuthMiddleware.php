<?php
namespace GymErpApi\Middleware;
use GymErpApi\Services\JwtService;
use WP_REST_Request;
class AuthMiddleware {
    public static function authenticate(WP_REST_Request $request) {
        $auth = $request->get_header('authorization');
        if (!$auth || !preg_match('/Bearer\s(\S+)/', $auth, $matches)) return new \WP_Error('jwt_missing', 'Missing Token', ['status'=>401]);
        $payload = JwtService::verify($matches[1]);
        if (!$payload) return new \WP_Error('jwt_invalid', 'Invalid or expired Token', ['status'=>401]);
        wp_set_current_user($payload['user_id']);
        return true;
    }
}
