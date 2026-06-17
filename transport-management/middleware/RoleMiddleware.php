<?php
namespace TransportManagementApi\Middleware;

if (!defined('ABSPATH')) {
    exit;
}

use WP_REST_Request;
use WP_Error;

class RoleMiddleware {
    
    /**
     * Check if the authenticated user has a specific capability.
     */
    public static function hasCapability(string $capability) {
        return function(WP_REST_Request $request) use ($capability) {
            // 1. Authenticate token
            $auth_result = AuthMiddleware::authenticate($request);
            if (is_wp_error($auth_result)) {
                return $auth_result;
            }

            // 2. Verify capability
            $user = wp_get_current_user();
            $has_access = false;
            if (is_a($user, 'WP_User')) {
                if (
                    in_array('administrator', (array)$user->roles) || 
                    in_array('transport_super_admin', (array)$user->roles) || 
                    current_user_can('manage_transport') || 
                    current_user_can($capability)
                ) {
                    $has_access = true;
                }
            }

            if (!$has_access) {
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
