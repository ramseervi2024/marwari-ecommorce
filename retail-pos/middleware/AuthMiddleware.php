<?php
namespace RetailPosApi\Middleware;

use RetailPosApi\Services\JwtService;
use WP_REST_Request;
use WP_Error;

class AuthMiddleware {
    
    /**
     * Authenticate the REST request using JWT
     */
    public static function authenticate(WP_REST_Request $request) {
        $auth_header = $request->get_header('Authorization');
        if (empty($auth_header)) {
            $auth_header = $request->get_header('X-Authorization');
        }
        
        // Apache / Hostinger authorization header bypass
        if (empty($auth_header)) {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['HTTP_X_AUTHORIZATION'])) {
                $auth_header = $_SERVER['HTTP_X_AUTHORIZATION'];
            } else {
                $all_headers = function_exists('getallheaders') ? getallheaders() : [];
                foreach ($all_headers as $name => $value) {
                    $lName = strtolower($name);
                    if ($lName === 'authorization' || $lName === 'x-authorization') {
                        $auth_header = $value;
                        break;
                    }
                }
            }
        }
        
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

        $user = get_userdata($payload['user_id']);
        if (!$user) {
            return new WP_Error(
                'rest_unauthorized',
                'Authenticated user does not exist.',
                ['status' => 401]
            );
        }

        // Set context
        wp_set_current_user($user->ID);

        return true;
    }
}
