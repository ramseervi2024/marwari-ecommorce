<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Services\AuthService;
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

        if (empty($params['username']) || empty($params['email']) || empty($params['name'])) {
            return $this->error('Validation failed: username, email, and name are required.');
        }

        $result = $this->authService->initiateRegister($params);

        if (is_wp_error($result)) {
            return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 400);
        }

        return $this->success('Verification code sent successfully to email.', $result, 200);
    }

    /**
     * POST /auth/register/verify
     */
    public function verifyRegister(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['email']) || empty($params['otp'])) {
            return $this->error('Validation failed: email and otp are required.');
        }

        $result = $this->authService->verifyRegister($params['email'], $params['otp']);

        if (is_wp_error($result)) {
            return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 400);
        }

        return $this->success('Registration completed successfully.', $result, 201);
    }

    /**
     * POST /auth/login/initiate
     */
    public function initiateLogin(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['username'])) {
            return $this->error('Validation failed: username or email is required.');
        }

        $result = $this->authService->initiateLogin($params['username']);

        if (is_wp_error($result)) {
            return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 400);
        }

        return $this->success('Login verification code sent successfully.', $result, 200);
    }

    /**
     * POST /auth/login
     */
    public function login(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['username'])) {
            return $this->error('Validation failed: username is required.');
        }

        if (!empty($params['password'])) {
            $result = $this->authService->login($params['username'], $params['password']);
        } elseif (!empty($params['otp'])) {
            $result = $this->authService->loginWithOtp($params['username'], $params['otp']);
        } else {
            return $this->error('Validation failed: either password or otp is required.');
        }

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
            'role' => !empty($user->roles) ? $user->roles[0] : '',
            'status' => get_user_meta($user->ID, 'workspace_user_status', true) ?: 'APPROVED'
        ]);
    }

    /**
     * POST /auth/logout
     */
    public function logout(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        delete_user_meta($user_id, 'workspace_refresh_token');
        AuthService::logActivity($user_id, 'LOGOUT', 'Successfully revoked refresh token and logged out');
        return $this->success('Logged out successfully');
    }

    /**
     * GET /auth/users
     */
    public function getUsers(WP_REST_Request $request) {
        $users = get_users([
            'orderby' => 'ID',
            'order' => 'DESC'
        ]);

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'name' => $user->display_name ?: $user->user_login,
                'role' => !empty($user->roles) ? $user->roles[0] : 'WordPress Core User',
                'status' => get_user_meta($user->ID, 'workspace_user_status', true) ?: 'APPROVED',
                'registered_at' => $user->user_registered
            ];
        }

        return $this->success('Users list fetched successfully', $data);
    }

    /**
     * POST /auth/users/status
     */
    public function updateUserStatus(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['user_id']) || empty($params['status'])) {
            return $this->error('Validation failed: user_id and status are required.');
        }

        $user_id = intval($params['user_id']);
        $status = strtoupper(sanitize_text_field($params['status']));

        $allowed_statuses = ['PENDING', 'APPROVED', 'HOLD', 'BLOCKED'];
        if (!in_array($status, $allowed_statuses)) {
            return $this->error('Invalid status value. Must be PENDING, APPROVED, HOLD, or BLOCKED.');
        }

        $user = get_userdata($user_id);
        if (!$user) {
            return $this->error('User not found.', [], 404);
        }

        if ($user_id === get_current_user_id()) {
            return $this->error('You cannot change your own status.');
        }

        update_user_meta($user_id, 'workspace_user_status', $status);
        AuthService::logActivity(get_current_user_id(), 'USER_STATUS_UPDATE', "Changed user ID $user_id status to $status");

        return $this->success("User status updated to $status successfully", [
            'user_id' => $user_id,
            'status' => $status
        ]);
    }

    /**
     * DELETE /auth/users/:id
     */
    public function deleteUser(WP_REST_Request $request) {
        $user_id = intval($request->get_param('id'));

        $user = get_userdata($user_id);
        if (!$user) {
            return $this->error('User not found.', [], 404);
        }

        if ($user_id === get_current_user_id()) {
            return $this->error('You cannot delete your own account.');
        }

        require_once ABSPATH . 'wp-admin/includes/user.php';
        $deleted = wp_delete_user($user_id);

        if (!$deleted) {
            return $this->error('Failed to delete user.');
        }

        AuthService::logActivity(get_current_user_id(), 'USER_DELETED', "Deleted user ID $user_id ($user->user_login)");

        return $this->success('User deleted successfully');
    }

    public function getSmtpSettings(WP_REST_Request $request) {
        return $this->success('Email settings retrieved successfully.', [
            'from_email' => get_option('workspace_smtp_from_email', ''),
            'from_name' => get_option('workspace_smtp_from_name', 'Aurbis Workspace ERP'),
            'subject' => get_option('workspace_email_subject', 'Aurbis Workspace ERP Verification Code'),
            'template' => get_option('workspace_email_template', "Hello {name},\n\nYour 6-digit verification code is: {otp}\n\nThis code is valid for 15 minutes.\n\nThank you,\nAurbis Workspace ERP Team"),
            'smtp_enabled' => get_option('workspace_smtp_enabled', 'no'),
            'smtp_host' => get_option('workspace_smtp_host', ''),
            'smtp_port' => get_option('workspace_smtp_port', '587'),
            'smtp_username' => get_option('workspace_smtp_username', ''),
            'smtp_password' => get_option('workspace_smtp_password', ''),
            'smtp_encryption' => get_option('workspace_smtp_encryption', 'tls')
        ]);
    }

    public function saveSmtpSettings(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $template = $params['template'] ?? '';

        if (empty($template) || strpos($template, '{otp}') === false) {
            return $this->error('Validation failed: The email template must contain the "{otp}" placeholder.');
        }

        update_option('workspace_smtp_from_email', sanitize_email($params['from_email'] ?? ''));
        update_option('workspace_smtp_from_name', sanitize_text_field($params['from_name'] ?? ''));
        update_option('workspace_email_subject', sanitize_text_field($params['subject'] ?? ''));
        update_option('workspace_email_template', sanitize_textarea_field($template));
        update_option('workspace_smtp_enabled', sanitize_text_field($params['smtp_enabled'] ?? 'no'));
        update_option('workspace_smtp_host', sanitize_text_field($params['smtp_host'] ?? ''));
        update_option('workspace_smtp_port', sanitize_text_field($params['smtp_port'] ?? '587'));
        update_option('workspace_smtp_username', sanitize_text_field($params['smtp_username'] ?? ''));
        update_option('workspace_smtp_password', sanitize_text_field($params['smtp_password'] ?? ''));
        update_option('workspace_smtp_encryption', sanitize_text_field($params['smtp_encryption'] ?? 'tls'));

        AuthService::logActivity(get_current_user_id(), 'EMAIL_SETTINGS_UPDATE', 'Updated email service configuration settings');

        return $this->success('Email settings saved successfully.');
    }

    public function testSmtpSettings(WP_REST_Request $request) {
        $params = $request->get_json_params();
        $test_email = sanitize_email($params['test_email'] ?? '');

        if (empty($test_email)) {
            return $this->error('A test email address is required.');
        }

        $mail_error = null;
        $fail_hook = function($error) use (&$mail_error) {
            if (is_wp_error($error)) {
                $mail_error = $error->get_error_message();
            }
        };
        add_action('wp_mail_failed', $fail_hook, 99);

        $subject = 'Aurbis Workspace ERP Test Mail';
        $message = "This is a test email from your Aurbis Workspace ERP email settings configuration.\n\nIf you are reading this message, your email delivery settings are working successfully!";
        
        $result = wp_mail($test_email, $subject, $message);

        remove_action('wp_mail_failed', $fail_hook, 99);

        if ($result && !$mail_error) {
            return $this->success('Test email sent successfully. Please check your inbox.');
        } else {
            $err_msg = $mail_error ?: 'WordPress wp_mail function returned false. Check your mail agent settings.';
            return $this->error('Failed to send test email: ' . $err_msg);
        }
    }
}
