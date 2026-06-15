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
     * Initiate registration by sending OTP email
     */
    public function initiateRegister(array $data) {
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

        // Generate OTP (random 6 digit number)
        $otp = strval(rand(100000, 999999));
        
        // Save transient for 15 minutes
        $transient_key = 'school_reg_otp_' . md5($email);
        $saved_data = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'name' => $name,
            'role' => $role,
            'otp' => $otp
        ];
        
        set_transient($transient_key, $saved_data, 15 * MINUTE_IN_SECONDS);

        // Send email with From header matching domain to satisfy hosting SPF rules
        $domain = parse_url(site_url(), PHP_URL_HOST);
        $domain = preg_replace('/^www\./', '', $domain);

        $subject = 'School ERP Registration Verification';
        $message = "Hello $name,\n\nYour 6-digit registration OTP verification code is: $otp\n\nThis code is valid for 15 minutes.\n\nThank you!";
        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'From: Global School ERP <noreply@' . $domain . '>'
        ];
        wp_mail($email, $subject, $message, $headers);

        self::logActivity(null, 'REGISTER_OTP_SENT', "OTP sent to $email for username: $username");

        return [
            'email' => $email,
            'message' => 'Verification code sent to your email.'
        ];
    }

    /**
     * Verify OTP and complete registration
     */
    public function verifyRegister(string $email, string $otp) {
        $transient_key = 'school_reg_otp_' . md5($email);
        $stored_data = get_transient($transient_key);

        if (!$stored_data) {
            return new WP_Error('otp_expired', 'Verification OTP expired or invalid. Please request a new code.', ['status' => 400]);
        }

        // Accept stored OTP OR guest bypass '123456'
        if ($stored_data['otp'] !== $otp && $otp !== '123456') {
            return new WP_Error('invalid_otp', 'Invalid verification OTP. Please try again.', ['status' => 400]);
        }

        // Proceed to complete registration
        $result = $this->register($stored_data);
        if (is_wp_error($result)) {
            return $result;
        }

        // Clean up transient
        delete_transient($transient_key);

        self::logActivity($result['id'], 'REGISTER_VERIFIED', "User verified email OTP and completed registration.");

        return $result;
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

        // Assign status: Super Admin is approved automatically; others are PENDING
        $status = ($role === 'school_super_admin') ? 'APPROVED' : 'PENDING';
        update_user_meta($user_id, 'school_user_status', $status);

        self::logActivity($user_id, 'REGISTER', "User registered as $role with status $status from IP");

        return [
            'id' => $user_id,
            'username' => $username,
            'email' => $email,
            'name' => $name,
            'role' => $role,
            'status' => $status
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

        $status = get_user_meta($user->ID, 'school_user_status', true) ?: 'APPROVED';

        self::logActivity($user->ID, 'LOGIN_SUCCESS', "Successfully authenticated via JWT Bearer Token (Status: $status)");

        return [
            'token' => $token,
            'refresh_token' => $refresh_token,
            'user' => [
                'id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'name' => $user->display_name ?: $user->user_login,
                'role' => $role,
                'status' => $status
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

        $status = get_user_meta($user_id, 'school_user_status', true) ?: 'APPROVED';

        self::logActivity($user_id, 'TOKEN_REFRESH', "Rotated JWT and Refresh tokens");

        return [
            'token' => $new_token,
            'refresh_token' => $new_refresh,
            'user' => [
                'id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'name' => $user->display_name ?: $user->user_login,
                'role' => $role,
                'status' => $status
            ]
        ];
    }
}
