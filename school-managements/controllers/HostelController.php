<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\EventRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class HostelController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new EventRepository();
    }

    /**
     * GET /hostels
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = ['type' => 'HOSTEL'];
        $result = $this->repository->findAll($params, ['id', 'title', 'status'], ['title'], $filters);
        
        foreach ($result['data'] as &$row) {
            $row['details'] = json_decode($row['description'], true);
        }

        return $this->success('Hostels and room allocations fetched successfully', $result);
    }

    /**
     * POST /hostels
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['hostel_name']) || empty($params['room_no'])) {
            return $this->error('Validation failed: hostel_name and room_no are required.');
        }

        $description = json_encode([
            'hostel_name' => sanitize_text_field($params['hostel_name']),
            'room_no' => sanitize_text_field($params['room_no']),
            'room_type' => sanitize_text_field($params['room_type'] ?? 'Shared'),
            'capacity' => (int)($params['capacity'] ?? 4),
            'student_id' => isset($params['student_id']) ? (int)$params['student_id'] : null
        ]);

        $data = [
            'type' => 'HOSTEL',
            'title' => sanitize_text_field($params['hostel_name']) . " Room " . sanitize_text_field($params['room_no']),
            'description' => $description,
            'event_date' => null,
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) {
            return $this->error('Failed to create hostel allocation.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_HOSTEL', "Created hostel allocation ID: $id");
        return $this->success('Hostel allocation created successfully', array_merge(['id' => $id], $data), 201);
    }

    /**
     * PUT /hostels/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $allocation = $this->repository->findById($id);

        if (!$allocation || $allocation['type'] !== 'HOSTEL') {
            return $this->error('Hostel allocation record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $current = json_decode($allocation['description'], true) ?: [];

        if (isset($params['hostel_name'])) {
            $current['hostel_name'] = sanitize_text_field($params['hostel_name']);
        }
        if (isset($params['room_no'])) {
            $current['room_no'] = sanitize_text_field($params['room_no']);
        }
        if (isset($params['room_type'])) {
            $current['room_type'] = sanitize_text_field($params['room_type']);
        }
        if (isset($params['capacity'])) {
            $current['capacity'] = (int)$params['capacity'];
        }
        if (isset($params['student_id'])) {
            $current['student_id'] = $params['student_id'] !== null ? (int)$params['student_id'] : null;
        }

        $data = [
            'title' => ($current['hostel_name'] ?? '') . " Room " . ($current['room_no'] ?? ''),
            'description' => json_encode($current),
            'updated_at' => current_time('mysql')
        ];

        $this->repository->update($id, $data, ['%s', '%s', '%s']);
        return $this->success('Hostel allocation updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /hostels/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $allocation = $this->repository->findById($id);

        if (!$allocation || $allocation['type'] !== 'HOSTEL') {
            return $this->error('Hostel allocation not found.', [], 404);
        }

        $this->repository->delete($id);
        return $this->success('Hostel allocation record deleted successfully');
    }
}
