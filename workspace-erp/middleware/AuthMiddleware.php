<?php
namespace WorkspaceErpApi\Middleware;

use WorkspaceErpApi\Services\JwtService;
use WP_REST_Request;
use WP_Error;

class AuthMiddleware {
    
    /**
     * Authenticate the REST request using JWT
     */
    public static function authenticate(WP_REST_Request $request) {
        $auth_header = $request->get_header('Authorization');
        
        if (empty($auth_header)) {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif ($request->get_header('X-Authorization')) {
                $auth_header = $request->get_header('X-Authorization');
            } elseif ($request->get_param('token')) {
                $auth_header = 'Bearer ' . $request->get_param('token');
            } elseif ($request->get_param('auth_token')) {
                $auth_header = 'Bearer ' . $request->get_param('auth_token');
            }
        }
        
        if (empty($auth_header)) {
            return new WP_Error('rest_unauthorized', 'Authorization header is missing.', ['status' => 401]);
        }

        // Handle case where token is passed directly without Bearer prefix
        if (!preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            $token = trim($auth_header);
        } else {
            $token = $matches[1];
        }
        $payload = JwtService::validateToken($token);

        if (!$payload || empty($payload['user_id'])) {
            return new WP_Error('rest_unauthorized', 'Expired or invalid signature token.', ['status' => 401]);
        }

        $user = get_userdata($payload['user_id']);
        if (!$user) {
            return new WP_Error('rest_unauthorized', 'Authenticated user does not exist.', ['status' => 401]);
        }

        wp_set_current_user($user->ID);
        return true;
    }
}
