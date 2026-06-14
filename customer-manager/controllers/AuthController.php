<?php
namespace CustomerManager\Controllers;

use CustomerManager\Services\JwtService;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use WP_User;

class AuthController {
    
    /**
     * Register a new user with specific API role.
     */
    public function register(WP_REST_Request $request): WP_REST_Response {
        $username = sanitize_user($request->get_param('username'));
        $email = sanitize_email($request->get_param('email'));
        $password = $request->get_param('password');
        $role = sanitize_text_field($request->get_param('role'));
        $name = sanitize_text_field($request->get_param('name'));

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($role)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Validation failed: username, email, password, and role are required.'
            ], 400);
        }

        // Limit roles to custom API roles only
        $allowed_roles = ['api_super_admin', 'api_manager', 'api_viewer'];
        if (!in_array($role, $allowed_roles)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Validation failed: Invalid role specified. Role must be api_super_admin, api_manager, or api_viewer.'
            ], 400);
        }

        if (username_exists($username)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Username already exists.'
            ], 409);
        }

        if (email_exists($email)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Email address already exists.'
            ], 409);
        }

        // Create user
        $user_id = wp_insert_user([
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => $password,
            'display_name' => $name ?: $username,
            'role' => $role
        ]);

        if (is_wp_error($user_id)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => $user_id->get_error_message()
            ], 500);
        }

        $user = get_userdata($user_id);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'role' => $role,
                'name' => $user->display_name
            ]
        ], 201);
    }

    /**
     * Authenticate and issue JWT.
     */
    public function login(WP_REST_Request $request): WP_REST_Response {
        $usernameOrEmail = sanitize_text_field($request->get_param('username'));
        $password = $request->get_param('password');

        if (empty($usernameOrEmail) || empty($password)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Username/email and password are required.'
            ], 400);
        }

        // Authenticate credentials via WordPress engine
        $user = wp_authenticate($usernameOrEmail, $password);

        if (is_wp_error($user)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid username/email or password.'
            ], 401);
        }

        // Get user role
        $role = !empty($user->roles) ? $user->roles[0] : '';

        // Check if role is an API role
        $allowed_roles = ['api_super_admin', 'api_manager', 'api_viewer', 'administrator'];
        if (!in_array($role, $allowed_roles)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Forbidden: You do not have permissions to access the Customer Management API.'
            ], 403);
        }

        // Generate Token
        $payload = [
            'user_id' => $user->ID,
            'email' => $user->user_email,
            'role' => $role
        ];
        
        $token = JwtService::generateToken($payload);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Authentication successful',
            'data' => [
                'token' => $token,
                'expires_in' => 86400, // 24 Hours
                'user' => [
                    'id' => $user->ID,
                    'username' => $user->user_login,
                    'email' => $user->user_email,
                    'name' => $user->display_name,
                    'role' => $role
                ]
            ]
        ], 200);
    }

    /**
     * Profile retrieve for active session.
     */
    public function me(WP_REST_Request $request): WP_REST_Response {
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        if (!$user) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => [
                'id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'name' => $user->display_name,
                'role' => !empty($user->roles) ? $user->roles[0] : ''
            ]
        ], 200);
    }

    /**
     * Refresh an active token.
     */
    public function refreshToken(WP_REST_Request $request): WP_REST_Response {
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        if (!$user) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid session.'
            ], 401);
        }

        $role = !empty($user->roles) ? $user->roles[0] : '';
        $payload = [
            'user_id' => $user->ID,
            'email' => $user->user_email,
            'role' => $role
        ];
        
        $token = JwtService::generateToken($payload);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $token,
                'expires_in' => 86400
            ]
        ], 200);
    }

    /**
     * Terminate session.
     */
    public function logout(WP_REST_Request $request): WP_REST_Response {
        // Since JWT is stateless, logout is handled client side by removing the token.
        // We return success validation.
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
