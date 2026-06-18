<?php
namespace PharmacyErpApi\Services;

if (!defined('ABSPATH')) exit;

class AuthService {
    public function login(string $username, string $password) {
        $user = wp_authenticate($username, $password);
        if (is_wp_error($user)) {
            return new \WP_Error('invalid_credentials', 'Invalid username or password.', ['status' => 401]);
        }
        $status = get_user_meta($user->ID, 'pharmacy_user_status', true) ?: 'APPROVED';
        if ($status !== 'APPROVED') {
            return new \WP_Error('account_inactive', "Account is $status. Contact administrator.", ['status' => 403]);
        }
        $role  = !empty($user->roles) ? $user->roles[0] : 'subscriber';
        $token = JwtService::generate($user->ID, $role);
        self::logActivity($user->ID, 'LOGIN', 'User logged in.');
        return [
            'token' => $token,
            'user'  => [
                'id'       => $user->ID,
                'username' => $user->user_login,
                'name'     => $user->display_name ?: $user->user_login,
                'email'    => $user->user_email,
                'role'     => $role,
                'status'   => $status,
            ]
        ];
    }

    public static function logActivity(?int $userId, string $actionType, string $description): void {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'pharmacy_activity_logs', [
            'user_id'     => $userId,
            'action_type' => $actionType,
            'description' => $description,
        ]);
    }
}
