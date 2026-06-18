<?php
namespace CrmManagementApi\Middleware;

use CrmManagementApi\Services\JwtService;
use WP_REST_Request;
use WP_Error;

class AuthMiddleware {
    
    /**
     * Authenticate the REST request using JWT
     */
    public static function authenticate(WP_REST_Request $request) {
        $auth_header = $request->get_header('Authorization');
        
        if (empty($auth_header)) {
            return new WP_Error(
                'rest_unauthorized',
                'Authorization header is missing.',
                ['status' => 401]
            );
        }

        if (!preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            return new WP_Error(
                'rest_unauthorized',
                'Invalid Authorization header format. Must be Bearer <token>.',
                ['status' => 401]
            );
        }

        $token = $matches[1];
        $payload = JwtService::validateToken($token);

        if (!$payload || empty($payload['user_id'])) {
            return new WP_Error(
                'rest_unauthorized',
                'Expired or invalid signature token.',
                ['status' => 401]
            );
        }

        // Verify user exists
        $user = get_userdata($payload['user_id']);
        if (!$user) {
            return new WP_Error(
                'rest_unauthorized',
                'Authenticated user does not exist.',
                ['status' => 401]
            );
        }

        // Verify status is APPROVED
        $status = get_user_meta($user->ID, 'crm_user_status', true) ?: 'APPROVED';
        if ($status === 'BLOCKED') {
            return new WP_Error(
                'rest_forbidden',
                'Your account has been blocked by the Administrator.',
                ['status' => 403]
            );
        } elseif ($status === 'HOLD') {
            return new WP_Error(
                'rest_forbidden',
                'Your account approval is currently on hold.',
                ['status' => 403]
            );
        }

        // Set context
        wp_set_current_user($user->ID);

        return true;
    }
}
