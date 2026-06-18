<?php
namespace PharmacyErpApi\Middleware;

use PharmacyErpApi\Services\JwtService;

if (!defined('ABSPATH')) exit;

class AuthMiddleware {
    public static function authenticate(\WP_REST_Request $request): bool|\WP_Error {
        $auth = $request->get_header('Authorization') ?: $request->get_header('authorization');
        if (!$auth || stripos($auth, 'Bearer ') !== 0) {
            return new \WP_Error('no_token', 'Authorization token required.', ['status' => 401]);
        }
        $token = trim(substr($auth, 7));
        $data  = JwtService::verify($token);
        if (!$data) {
            return new \WP_Error('invalid_token', 'Invalid or expired token.', ['status' => 401]);
        }
        $user = get_user_by('id', $data['sub']);
        if (!$user) {
            return new \WP_Error('user_not_found', 'User not found.', ['status' => 401]);
        }
        $status = get_user_meta($data['sub'], 'pharmacy_user_status', true) ?: 'APPROVED';
        if ($status !== 'APPROVED') {
            return new \WP_Error('account_inactive', 'Account is not active.', ['status' => 403]);
        }
        wp_set_current_user($data['sub']);
        return true;
    }
}
