<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Services\AuthService;
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

        if (empty($params['username']) || empty($params['email']) || empty($params['password']) || empty($params['name'])) {
            return $this->error('Validation failed: username, email, password, and name are required.');
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
     * POST /auth/login
     */
    public function login(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['username']) || empty($params['password'])) {
            return $this->error('Validation failed: username and password are required.');
        }

        $result = $this->authService->login($params['username'], $params['password']);

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
            'status' => get_user_meta($user->ID, 'school_user_status', true) ?: 'APPROVED'
        ]);
    }

    /**
     * POST /auth/logout
     */
    public function logout(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        delete_user_meta($user_id, 'school_refresh_token');
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
                'status' => get_user_meta($user->ID, 'school_user_status', true) ?: 'APPROVED',
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

        // Prevent modifying own status
        if ($user_id === get_current_user_id()) {
            return $this->error('You cannot change your own status.');
        }

        update_user_meta($user_id, 'school_user_status', $status);
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

        // Prevent self deletion
        if ($user_id === get_current_user_id()) {
            return $this->error('You cannot delete your own account.');
        }

        // Delete WordPress user
        require_once ABSPATH . 'wp-admin/includes/user.php';
        $deleted = wp_delete_user($user_id);

        if (!$deleted) {
            return $this->error('Failed to delete user.');
        }

        AuthService::logActivity(get_current_user_id(), 'USER_DELETED', "Deleted user ID $user_id ($user->user_login)");

        return $this->success('User deleted successfully');
    }
}
