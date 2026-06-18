<?php
namespace PharmacyErpApi\Controllers;

use PharmacyErpApi\Services\AuthService;
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
        if (!$user) return $this->error('User not found.', [], 404);
        return $this->success('User details.', [
            'id' => $user->ID, 'username' => $user->user_login, 'email' => $user->user_email,
            'name' => $user->display_name ?: $user->user_login,
            'role' => !empty($user->roles) ? $user->roles[0] : '',
            'status' => get_user_meta($user->ID, 'pharmacy_user_status', true) ?: 'APPROVED',
        ]);
    }

    public function logout(WP_REST_Request $request) {
        AuthService::logActivity(get_current_user_id(), 'LOGOUT', 'User logged out.');
        return $this->success('Logged out.');
    }

    public function getUsers(WP_REST_Request $request) {
        $users = get_users(['role__in' => ['pharmacy_admin', 'pharmacy_staff', 'administrator']]);
        $list = [];
        foreach ($users as $u) {
            $list[] = [
                'id' => $u->ID, 'username' => $u->user_login, 'email' => $u->user_email,
                'name' => $u->display_name, 'role' => !empty($u->roles) ? $u->roles[0] : '',
                'status' => get_user_meta($u->ID, 'pharmacy_user_status', true) ?: 'APPROVED',
            ];
        }
        return $this->success('Users list.', $list);
    }

    public function updateUserStatus(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['user_id']) || empty($p['status'])) return $this->error('user_id and status required.');
        if (!in_array($p['status'], ['APPROVED','PENDING','BLOCKED'])) return $this->error('Invalid status.');
        update_user_meta(intval($p['user_id']), 'pharmacy_user_status', $p['status']);
        return $this->success("Status updated to {$p['status']}.");
    }

    public function getSmtpSettings(WP_REST_Request $request) {
        return $this->success('SMTP settings.', [
            'smtp_enabled'    => get_option('pharmacy_smtp_enabled', 'no'),
            'smtp_host'       => get_option('pharmacy_smtp_host', ''),
            'smtp_port'       => get_option('pharmacy_smtp_port', '587'),
            'smtp_username'   => get_option('pharmacy_smtp_username', ''),
            'smtp_encryption' => get_option('pharmacy_smtp_encryption', 'tls'),
            'smtp_from_email' => get_option('pharmacy_smtp_from_email', ''),
            'smtp_from_name'  => get_option('pharmacy_smtp_from_name', 'Pharmacy ERP'),
        ]);
    }

    public function saveSmtpSettings(WP_REST_Request $request) {
        $p = $request->get_json_params();
        foreach (['smtp_enabled','smtp_host','smtp_port','smtp_encryption','smtp_from_email','smtp_from_name','smtp_username'] as $k) {
            if (isset($p[$k])) update_option("pharmacy_{$k}", sanitize_text_field($p[$k]));
        }
        if (isset($p['smtp_password']) && $p['smtp_password'] !== '******') {
            update_option('pharmacy_smtp_password', sanitize_text_field($p['smtp_password']));
        }
        return $this->success('SMTP settings saved.');
    }
}
