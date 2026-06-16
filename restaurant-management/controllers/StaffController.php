<?php
namespace RestaurantManagementApi\Controllers;

use RestaurantManagementApi\Repositories\StaffRepository;
use WP_REST_Request;

class StaffController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new StaffRepository();
    }

    public function getStaff(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 50);
        $offset = intval($request->get_param('offset') ?: 0);
        
        $staff = $this->repository->all($limit, $offset);
        return $this->success('Staff shifts retrieved successfully.', $staff);
    }

    public function createStaff(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['name']) || empty($params['role'])) {
            return $this->error('Validation failed: name and role are required.');
        }

        $data = [
            'name' => sanitize_text_field($params['name']),
            'role' => sanitize_text_field($params['role']),
            'shift_start' => sanitize_text_field($params['shift_start'] ?? '09:00'),
            'shift_end' => sanitize_text_field($params['shift_end'] ?? '17:00'),
            'salary' => floatval($params['salary'] ?? 0.00),
            'attendance_status' => sanitize_text_field($params['attendance_status'] ?? 'Absent')
        ];

        $id = $this->repository->create($data);
        if (!$id) {
            return $this->error('Failed to create staff shift profile.');
        }

        $data['id'] = $id;
        return $this->success('Staff shift registered successfully.', $data, 201);
    }

    public function updateStaff(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $params = $request->get_json_params();

        $staff = $this->repository->find($id);
        if (!$staff) {
            return $this->error('Staff profile not found.', [], 404);
        }

        $data = [];
        if (isset($params['name'])) $data['name'] = sanitize_text_field($params['name']);
        if (isset($params['role'])) $data['role'] = sanitize_text_field($params['role']);
        if (isset($params['shift_start'])) $data['shift_start'] = sanitize_text_field($params['shift_start']);
        if (isset($params['shift_end'])) $data['shift_end'] = sanitize_text_field($params['shift_end']);
        if (isset($params['salary'])) $data['salary'] = floatval($params['salary']);
        if (isset($params['attendance_status'])) $data['attendance_status'] = sanitize_text_field($params['attendance_status']);

        if (empty($data)) {
            return $this->error('No fields provided to update.');
        }

        if (!$this->repository->update($id, $data)) {
            return $this->error('Failed to update staff shift details.');
        }

        return $this->success('Staff shift updated successfully.', array_merge($staff, $data));
    }

    public function deleteStaff(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $staff = $this->repository->find($id);
        if (!$staff) {
            return $this->error('Staff profile not found.', [], 404);
        }

        if (!$this->repository->delete($id)) {
            return $this->error('Failed to delete staff profile.');
        }

        return $this->success('Staff profile deleted successfully.');
    }
}
