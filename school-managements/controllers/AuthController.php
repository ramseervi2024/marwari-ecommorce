<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class AuthController extends BaseController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    /**
     * POST /auth/register
     */
    public function register(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['username']) || empty($params['email']) || empty($params['password']) || empty($params['name'])) {
            return $this->error('Validation failed: username, email, password, and name are required.');
        }

        $result = $this->authService->register($params);

        if (is_wp_error($result)) {
            return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 400);
        }

        return $this->success('User registered successfully', $result, 201);
    }

    /**
     * POST /auth/login
     */
    public function login(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['username']) || empty($params['password'])) {
            return $this->error('Validation failed: username and password are required.');
        }

        $result = $this->authService->login($params['username'], $params['password']);

        if (is_wp_error($result)) {
            return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 401);
        }

        return $this->success('Authentication successful', $result);
    }

    /**
     * POST /auth/refresh-token
     */
    public function refreshToken(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['refresh_token'])) {
            return $this->error('Validation failed: refresh_token is required.');
        }

        $result = $this->authService->refreshToken($params['refresh_token']);

        if (is_wp_error($result)) {
            return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 401);
        }

        return $this->success('Token refreshed successfully', $result);
    }

    /**
     * GET /auth/me
     */
    public function me(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        if (!$user) {
            return $this->error('User session not found.', [], 404);
        }

        return $this->success('Current user profile fetched successfully', [
            'id' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'name' => $user->display_name ?: $user->user_login,
            'role' => !empty($user->roles) ? $user->roles[0] : ''
        ]);
    }

    /**
     * POST /auth/logout
     */
    public function logout(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        delete_user_meta($user_id, 'school_refresh_token');
        AuthService::logActivity($user_id, 'LOGOUT', 'Successfully revoked refresh token and logged out');
        return $this->success('Logged out successfully');
    }
}
