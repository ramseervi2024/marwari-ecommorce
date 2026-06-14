<?php
namespace CustomerManager\Middleware;

use CustomerManager\Services\JwtService;
use WP_REST_Request;
use WP_Error;

class AuthMiddleware {
    
    /**
     * Authenticate the request via JWT Bearer token.
     * Sets the global WordPress current user context if valid.
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

        // Parse Bearer Token
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

        // Verify user exists in WordPress
        $user = get_userdata($payload['user_id']);
        if (!$user) {
            return new WP_Error(
                'rest_unauthorized',
                'Authenticated user does not exist.',
                ['status' => 401]
            );
        }

        // Establish WordPress session context for current request
        wp_set_current_user($user->ID);

        return true;
    }
}
