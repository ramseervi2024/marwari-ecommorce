<?php
namespace FleetTrackPro\Controllers;

use FleetTrackPro\Services\AuthService;
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
        
        // Validation rules
        if (empty($params['username']) || empty($params['email']) || empty($params['password']) || empty($params['name'])) {
            return $this->error('Validation failed: Required fields missing.', ['fields' => 'username, email, password, name are required.']);
        }

        if (!is_email($params['email'])) {
            return $this->error('Validation failed: Invalid email address.');
        }

        if (strlen($params['password']) < 6) {
            return $this->error('Validation failed: Password must be at least 6 characters.');
        }

        $result = $this->authService->register($params);

        if (is_wp_error($result)) {
            $data = $result->get_error_data();
            $status = isset($data['status']) ? $data['status'] : 400;
            return $this->error($result->get_error_message(), [], $status);
        }

        return $this->success('Account registered successfully', $result, 201);
    }

    /**
     * POST /auth/login
     */
    public function login(WP_REST_Request $request) {
        $params = $request->get_json_params();
        
        if (empty($params['username']) || empty($params['password'])) {
            return $this->error('Username/Email and Password are required.');
        }

        $result = $this->authService->login($params['username'], $params['password']);

        if (is_wp_error($result)) {
            $data = $result->get_error_data();
            $status = isset($data['status']) ? $data['status'] : 401;
            return $this->error($result->get_error_message(), [], $status);
        }

        return $this->success('Login successful', $result);
    }

    /**
     * POST /auth/refresh-token
     */
    public function refreshToken(WP_REST_Request $request) {
        $params = $request->get_json_params();
        
        if (empty($params['refresh_token'])) {
            return $this->error('Refresh token is required.');
        }

        $result = $this->authService->refreshToken($params['refresh_token']);

        if (is_wp_error($result)) {
            $data = $result->get_error_data();
            $status = isset($data['status']) ? $data['status'] : 401;
            return $this->error($result->get_error_message(), [], $status);
        }

        return $this->success('Tokens rotated successfully', $result);
    }

    /**
     * GET /auth/me
     */
    public function me(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        if (!$user) {
            return $this->error('User not authenticated.', [], 401);
        }

        return $this->success('Active user profile details', [
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
        if ($user_id) {
            delete_user_meta($user_id, 'fleet_refresh_token');
            AuthService::logActivity($user_id, 'LOGOUT', "Logged out of session");
        }
        return $this->success('Successfully signed out.');
    }
}
