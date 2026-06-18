<?php
namespace GymErpApi\Controllers;
use GymErpApi\Services\AuthService;
use WP_REST_Request;

class AuthController extends BaseController {
    private $authService;
    public function __construct() { $this->authService = new AuthService(); }

    public function login(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['username']) || empty($p['password'])) return $this->error('Username and password required.');
        $result = $this->authService->login($p['username'], $p['password']);
        if (is_wp_error($result)) return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 401);
        return $this->success('Login successful.', $result);
    }
    public function me(WP_REST_Request $request) {
        $user = get_userdata(get_current_user_id());
        if (!$user) return $this->error('Not found.', [], 404);
        return $this->success('User.', [
            'id' => $user->ID, 'username' => $user->user_login, 'name' => $user->display_name,
            'role' => !empty($user->roles) ? $user->roles[0] : ''
        ]);
    }
    public function logout(WP_REST_Request $request) {
        AuthService::logActivity(get_current_user_id(), 'LOGOUT');
        return $this->success('Logged out.');
    }
}
