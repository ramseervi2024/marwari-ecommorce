<?php
namespace HrManagementApi\Controllers;

use HrManagementApi\Services\AuthService;
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
            return $this->error($result->get_error_message());
        }

        return $this->success($result['message'], ['email' => $result['email']]);
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
            return $this->error($result->get_error_message());
        }

        return $this->success('Registration completed successfully. Account is pending admin approval.', $result, 201);
    }

    /**
     * POST /auth/login/initiate
     */
    public function initiateLogin(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['username_or_email'])) {
            return $this->error('Validation failed: username_or_email is required.');
        }

        $result = $this->authService->initiateLogin($params['username_or_email']);
        if (is_wp_error($result)) {
            return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 400);
        }

        return $this->success($result['message'], ['email' => $result['email']]);
    }

    /**
     * POST /auth/login
     */
    public function login(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['username'])) {
            return $this->error('Validation failed: username is required.');
        }

        if (!empty($params['otp'])) {
            $result = $this->authService->loginWithOtp($params['username'], $params['otp']);
        } else {
            if (empty($params['password'])) {
                return $this->error('Validation failed: password or otp is required.');
            }
            $result = $this->authService->login($params['username'], $params['password']);
        }

        if (is_wp_error($result)) {
            return $this->error($result->get_error_message(), [], $result->get_error_data()['status'] ?? 401);
        }

        return $this->success('Login successful.', $result);
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
            return $this->error($result->get_error_message(), [], 401);
        }

        return $this->success('Token refreshed successfully.', $result);
    }

    /**
     * GET /auth/me
     */
    public function me(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        if (!$user) {
            return $this->error('User not found.', [], 404);
        }

        $role = !empty($user->roles) ? $user->roles[0] : '';
        $status = get_user_meta($user_id, 'hr_user_status', true) ?: 'APPROVED';

        return $this->success('Current user details retrieved.', [
            'id' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'name' => $user->display_name ?: $user->user_login,
            'role' => $role,
            'status' => $status
        ]);
    }

    /**
     * POST /auth/logout
     */
    public function logout(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        delete_user_meta($user_id, 'hr_refresh_token');
        AuthService::logActivity($user_id, 'LOGOUT', 'User logged out successfully.');
        return $this->success('Logout successful.');
    }

    /**
     * GET /auth/smtp
     */
    public function getSmtpSettings(WP_REST_Request $request) {
        return $this->success('SMTP settings retrieved.', [
            'smtp_enabled' => get_option('hr_smtp_enabled', 'no'),
            'smtp_host' => get_option('hr_smtp_host', ''),
            'smtp_port' => get_option('hr_smtp_port', '587'),
            'smtp_username' => get_option('hr_smtp_username', ''),
            'smtp_encryption' => get_option('hr_smtp_encryption', 'tls'),
            'smtp_from_email' => get_option('hr_smtp_from_email', ''),
            'smtp_from_name' => get_option('hr_smtp_from_name', 'HR & Payroll ERP')
        ]);
    }

    /**
     * POST /auth/smtp
     */
    public function saveSmtpSettings(WP_REST_Request $request) {
        $params = $request->get_json_params();

        update_option('hr_smtp_enabled', sanitize_text_field($params['smtp_enabled'] ?? 'no'));
        update_option('hr_smtp_host', sanitize_text_field($params['smtp_host'] ?? ''));
        update_option('hr_smtp_port', sanitize_text_field($params['smtp_port'] ?? '587'));
        update_option('hr_smtp_username', sanitize_text_field($params['smtp_username'] ?? ''));
        if (isset($params['smtp_password']) && $params['smtp_password'] !== '******') {
            update_option('hr_smtp_password', sanitize_text_field($params['smtp_password']));
        }
        update_option('hr_smtp_encryption', sanitize_text_field($params['smtp_encryption'] ?? 'tls'));
        update_option('hr_smtp_from_email', sanitize_email($params['smtp_from_email'] ?? ''));
        update_option('hr_smtp_from_name', sanitize_text_field($params['smtp_from_name'] ?? 'HR & Payroll ERP'));

        AuthService::logActivity(get_current_user_id(), 'SMTP_CONFIG_UPDATE', 'Updated SMTP Configuration settings');

        return $this->success('SMTP settings saved successfully.');
    }

    /**
     * POST /auth/smtp/test
     */
    public function testSmtpSettings(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['test_email'])) {
            return $this->error('test_email is required.');
        }

        $email = sanitize_email($params['test_email']);
        $subject = 'HR ERP - SMTP Connection Test';
        $message = 'This is a test email from your HR & Payroll ERP SMTP configuration. If you receive this, your SMTP settings are working perfectly!';
        
        $sent = wp_mail($email, $subject, $message);
        if ($sent) {
            return $this->success('Test email sent successfully. Please check your inbox.');
        } else {
            return $this->error('Failed to send test email. Check your SMTP settings and PHP error logs.');
        }
    }

    /**
     * GET /auth/users
     */
    public function getUsers(WP_REST_Request $request) {
        $users = get_users([
            'role__in' => ['hr_super_admin', 'hr_manager', 'hr_accountant', 'hr_employee']
        ]);

        $list = [];
        foreach ($users as $u) {
            $list[] = [
                'id' => $u->ID,
                'username' => $u->user_login,
                'email' => $u->user_email,
                'name' => $u->display_name ?: $u->user_login,
                'role' => !empty($u->roles) ? $u->roles[0] : '',
                'status' => get_user_meta($u->ID, 'hr_user_status', true) ?: 'APPROVED'
            ];
        }

        return $this->success('Users list retrieved.', $list);
    }

    /**
     * POST /auth/users/status
     */
    public function updateUserStatus(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['user_id']) || empty($params['status'])) {
            return $this->error('user_id and status are required.');
        }

        $uid = intval($params['user_id']);
        $status = sanitize_text_field($params['status']);
        
        if (!in_array($status, ['APPROVED', 'PENDING', 'BLOCKED', 'HOLD'])) {
            return $this->error('Invalid status value.');
        }

        $user = get_userdata($uid);
        if (!$user) {
            return $this->error('User not found.');
        }

        update_user_meta($uid, 'hr_user_status', $status);
        AuthService::logActivity(get_current_user_id(), 'USER_STATUS_CHANGE', "Changed status of user $user->user_login to $status");

        return $this->success("User status updated to $status successfully.");
    }

    /**
     * DELETE /auth/users/:id
     */
    public function deleteUser(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if ($id === get_current_user_id()) {
            return $this->error('You cannot delete your own account.');
        }

        $user = get_userdata($id);
        if (!$user) {
            return $this->error('User not found.', [], 404);
        }

        // Delete associated employee profiles and balance info first
        global $wpdb;
        $table_employees = $wpdb->prefix . 'hr_employees';
        $emp = $wpdb->get_row($wpdb->prepare("SELECT id FROM $table_employees WHERE user_id = %d", $id));
        if ($emp) {
            $wpdb->delete($table_employees, ['id' => $emp->id]);
            $wpdb->delete($wpdb->prefix . 'hr_leave_balances', ['employee_id' => $emp->id]);
            $wpdb->delete($wpdb->prefix . 'hr_salaries', ['employee_id' => $emp->id]);
        }

        require_once ABSPATH . 'wp-admin/includes/user.php';
        wp_delete_user($id);
        
        AuthService::logActivity(get_current_user_id(), 'USER_DELETE', "Deleted user account: $user->user_login");

        return $this->success('User deleted successfully.');
    }
}
