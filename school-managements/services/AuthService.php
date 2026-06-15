<?php
namespace SchoolManagementApi\Services;

use WP_Error;

class AuthService {
    
    /**
     * Create a log entry in activity logs
     */
    public static function logActivity(?int $user_id, string $action, string $details = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'school_activity_logs';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        $wpdb->insert(
            $table,
            [
                'user_id' => $user_id,
                'action' => $action,
                'details' => $details,
                'ip_address' => $ip,
                'created_at' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s', '%s']
        );
    }

    /**
     * Register a new user and assign custom school role
     */
    public function register(array $data) {
        $username = sanitize_user($data['username']);
        $email = sanitize_email($data['email']);
        $password = $data['password'];
        $name = sanitize_text_field($data['name']);
        $role = sanitize_text_field($data['role'] ?? 'school_student');

        // Validate role is allowed
        $allowed_roles = ['school_super_admin', 'school_principal', 'school_teacher', 'school_accountant', 'school_parent', 'school_student'];
        if (!in_array($role, $allowed_roles)) {
            return new WP_Error('invalid_role', 'Invalid role requested.', ['status' => 400]);
        }

        if (username_exists($username)) {
            return new WP_Error('username_exists', 'Username already exists.', ['status' => 400]);
        }

        if (email_exists($email)) {
            return new WP_Error('email_exists', 'Email already exists.', ['status' => 400]);
        }

        $user_id = wp_insert_user([
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => $password,
            'display_name' => $name,
            'first_name' => $name,
            'role' => $role
        ]);

        if (is_wp_error($user_id)) {
            return $user_id;
        }

        update_user_meta($user_id, 'nickname', $name);

        self::logActivity($user_id, 'REGISTER', "User registered as $role from IP");

        return [
            'id' => $user_id,
            'username' => $username,
            'email' => $email,
            'name' => $name,
            'role' => $role
        ];
    }

    /**
     * Authenticate and return JWT token
     */
    public function login(string $username, string $password) {
        $user = wp_authenticate($username, $password);
        
        if (is_wp_error($user)) {
            self::logActivity(null, 'LOGIN_FAILED', "Failed login attempt for username: $username");
            return new WP_Error('invalid_credentials', 'Invalid username/email or password.', ['status' => 401]);
        }

        // Check if user has school role or admin role
        $role = !empty($user->roles) ? $user->roles[0] : '';
        $allowed_roles = ['administrator', 'school_super_admin', 'school_principal', 'school_teacher', 'school_accountant', 'school_parent', 'school_student'];
        if (!in_array($role, $allowed_roles)) {
            self::logActivity($user->ID, 'LOGIN_DENIED', "User has no authorization for School ERP Portal");
            return new WP_Error('forbidden_role', 'You are not authorized to access this portal.', ['status' => 403]);
        }

        $payload = [
            'user_id' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'role' => $role
        ];

        $token = JwtService::generateToken($payload);
        $refresh_token = JwtService::generateToken(['user_id' => $user->ID, 'type' => 'refresh'], 604800); // 7 days

        // Store refresh token in user meta for rotation validation
        update_user_meta($user->ID, 'school_refresh_token', $refresh_token);

        self::logActivity($user->ID, 'LOGIN_SUCCESS', "Successfully authenticated via JWT Bearer Token");

        return [
            'token' => $token,
            'refresh_token' => $refresh_token,
            'user' => [
                'id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'name' => $user->display_name ?: $user->user_login,
                'role' => $role
            ]
        ];
    }

    /**
     * Refresh expired JWT token
     */
    public function refreshToken(string $refresh_token) {
        $payload = JwtService::validateToken($refresh_token);
        if (!$payload || empty($payload['user_id']) || ($payload['type'] ?? '') !== 'refresh') {
            return new WP_Error('invalid_token', 'Invalid or expired refresh token.', ['status' => 401]);
        }

        $user_id = $payload['user_id'];
        $stored_token = get_user_meta($user_id, 'school_refresh_token', true);

        if ($stored_token !== $refresh_token) {
            return new WP_Error('token_revoked', 'Refresh token has been revoked or rotated.', ['status' => 401]);
        }

        $user = get_userdata($user_id);
        if (!$user) {
            return new WP_Error('invalid_user', 'User does not exist.', ['status' => 401]);
        }

        $role = !empty($user->roles) ? $user->roles[0] : '';
        $new_payload = [
            'user_id' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'role' => $role
        ];

        $new_token = JwtService::generateToken($new_payload);
        $new_refresh = JwtService::generateToken(['user_id' => $user->ID, 'type' => 'refresh'], 604800);

        update_user_meta($user_id, 'school_refresh_token', $new_refresh);

        self::logActivity($user_id, 'TOKEN_REFRESH', "Rotated JWT and Refresh tokens");

        return [
            'token' => $new_token,
            'refresh_token' => $new_refresh
        ];
    }
}
