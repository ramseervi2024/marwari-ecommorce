<?php
namespace CustomerManager\Middleware;

use WP_REST_Request;
use WP_Error;

class RoleMiddleware {
    
    /**
     * Check if the authenticated user has a specific capability.
     */
    public static function hasCapability(string $capability) {
        return function(WP_REST_Request $request) use ($capability) {
            // 1. Authenticate JWT token first
            $auth_result = AuthMiddleware::authenticate($request);
            if (is_wp_error($auth_result)) {
                return $auth_result;
            }

            // 2. Check capability
            if (!current_user_can($capability)) {
                return new WP_Error(
                    'rest_forbidden',
                    'Access Forbidden: Insufficient role permissions.',
                    ['status' => 403]
                );
            }

            return true;
        };
    }
}
