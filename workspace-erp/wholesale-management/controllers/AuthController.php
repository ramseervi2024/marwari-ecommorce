<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Services\AuthService;
use WholesaleErp\Services\JwtService;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class AuthController extends BaseController {
    private $authService;
    public function __construct() {
        $this->authService = new AuthService();
    }

    public function login(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['username']) || empty($p['password'])) {
            return $this->error('Username and password required.');
        }
        $result = $this->authService->login($p['username'], $p['password']);
        if (is_wp_error($result)) {
            return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 401);
        }
        return $this->success('Login successful.', $result);
    }

    public function register(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['username']) || empty($p['password']) || empty($p['email'])) {
            return $this->error('Username, password, and email required.');
        }
        if (username_exists($p['username'])) {
            return $this->error('Username already exists.');
        }
        if (email_exists($p['email'])) {
            return $this->error('Email already exists.');
        }
        $uid = wp_create_user($p['username'], $p['password'], $p['email']);
        if (is_wp_error($uid)) {
            return $this->error($uid->get_error_message());
        }
        $role = $p['role'] ?? 'wholesale_dealer';
        $user = new \WP_User($uid);
        $user->set_role($role);
        
        $name = $p['name'] ?? $p['username'];
        wp_update_user(['ID' => $uid, 'display_name' => $name]);
        update_user_meta($uid, 'wholesale_user_status', 'APPROVED');
        
        AuthService::logActivity($uid, 'REGISTER', 'User registered');
        return $this->success('Registered successfully.', ['id' => $uid, 'username' => $p['username']]);
    }

    public function me(WP_REST_Request $request) {
        $user = get_userdata(get_current_user_id());
        if (!$user) return $this->error('Not found.', [], 404);
        return $this->success('User.', [
            'id' => $user->ID,
            'username' => $user->user_login,
            'name' => $user->display_name ?: $user->user_login,
            'role' => !empty($user->roles) ? $user->roles[0] : ''
        ]);
    }

    public function logout(WP_REST_Request $request) {
        AuthService::logActivity(get_current_user_id(), 'LOGOUT');
        return $this->success('Logged out.');
    }

    public function refreshToken(WP_REST_Request $request) {
        $uid = get_current_user_id();
        if (!$uid) return $this->error('Not authenticated.', [], 401);
        $user = get_userdata($uid);
        $token = JwtService::generate([
            'user_id'  => $user->ID,
            'username' => $user->user_login,
            'role'     => !empty($user->roles) ? $user->roles[0] : '',
        ]);
        return $this->success('Token refreshed.', ['token' => $token]);
    }
}
