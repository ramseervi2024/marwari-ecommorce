<?php
namespace WholesaleErp\Services;

if (!defined('ABSPATH')) exit;

class AuthService {
    public function login(string $username, string $password) {
        $user = wp_authenticate($username, $password);
        if (is_wp_error($user)) return $user;
        $status = get_user_meta($user->ID, 'wholesale_user_status', true) ?: 'APPROVED';
        if ($status !== 'APPROVED') {
            return new \WP_Error('blocked', 'Account is pending or blocked', ['status' => 403]);
        }
        $token = JwtService::generate([
            'user_id'  => $user->ID,
            'username' => $user->user_login,
            'role'     => !empty($user->roles) ? $user->roles[0] : '',
        ]);
        self::logActivity($user->ID, 'LOGIN', 'User logged in from ' . ($_SERVER['REMOTE_ADDR'] ?? ''));
        return [
            'token' => $token,
            'user'  => [
                'id'       => $user->ID,
                'username' => $user->user_login,
                'email'    => $user->user_email,
                'name'     => $user->display_name ?: $user->user_login,
                'role'     => !empty($user->roles) ? $user->roles[0] : '',
                'status'   => $status,
            ]
        ];
    }

    public static function logActivity(int $userId, string $action, string $details = '') {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wholesale_activity_logs', [
            'user_id'    => $userId,
            'action'     => $action,
            'details'    => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
