<?php
namespace HrManagementApi\Middleware;

use WP_REST_Request;

class RoleMiddleware {
    
    /**
     * Return a callback that checks if the request is authenticated and has the required capability.
     */
    public static function hasCapability(string $capability) {
        return function (WP_REST_Request $request) use ($capability) {
            // 1. Authenticate JWT token
            $auth_result = AuthMiddleware::authenticate($request);
            if (is_wp_error($auth_result)) {
                return $auth_result;
            }

            // 2. Check capability
            if (!current_user_can($capability)) {
                return new \WP_Error(
                    'rest_forbidden',
                    'Access Forbidden: You do not have permission to access this resource.',
                    ['status' => 403]
                );
            }

            return true;
        };
    }
}
