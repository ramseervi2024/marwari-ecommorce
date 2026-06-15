<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\EventRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class TimetableController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new EventRepository();
    }

    /**
     * GET /timetable
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = ['type' => 'TIMETABLE'];
        if (!empty($params['class_id'])) {
            $filters['description'] = '%' . $params['class_id'] . '%'; // check class_id inside json payload
        }
        
        $result = $this->repository->findAll($params, ['id', 'title'], [], $filters);
        
        // Decode JSON payload in description
        foreach ($result['data'] as &$row) {
            $row['details'] = json_decode($row['description'], true);
        }
        
        return $this->success('Timetables fetched successfully', $result);
    }

    /**
     * POST /timetable
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['class_id']) || empty($params['day']) || empty($params['slots'])) {
            return $this->error('Validation failed: class_id, day, and slots are required.');
        }

        $title = "Class " . $params['class_id'] . " - " . $params['day'] . " Timetable";
        $description = json_encode([
            'class_id' => (int)$params['class_id'],
            'day' => sanitize_text_field($params['day']),
            'slots' => $params['slots'] // Array of slots: { start_time, end_time, subject_id, teacher_id }
        ]);

        $data = [
            'type' => 'TIMETABLE',
            'title' => $title,
            'description' => $description,
            'event_date' => null,
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) {
            return $this->error('Failed to create timetable.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_TIMETABLE', "Created timetable for class ID: {$params['class_id']}");
        return $this->success('Timetable created successfully', array_merge(['id' => $id], $data), 201);
    }

    /**
     * PUT /timetable/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $timetable = $this->repository->findById($id);

        if (!$timetable || $timetable['type'] !== 'TIMETABLE') {
            return $this->error('Timetable entry not found.', [], 404);
        }

        $params = $request->get_json_params();
        $current_details = json_decode($timetable['description'], true) ?: [];

        if (isset($params['class_id'])) {
            $current_details['class_id'] = (int)$params['class_id'];
        }
        if (isset($params['day'])) {
            $current_details['day'] = sanitize_text_field($params['day']);
        }
        if (isset($params['slots'])) {
            $current_details['slots'] = $params['slots'];
        }

        $data = [
            'title' => "Class " . ($current_details['class_id'] ?? '') . " - " . ($current_details['day'] ?? '') . " Timetable",
            'description' => json_encode($current_details),
            'updated_at' => current_time('mysql')
        ];

        $this->repository->update($id, $data, ['%s', '%s', '%s']);
        
        return $this->success('Timetable updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /timetable/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $timetable = $this->repository->findById($id);

        if (!$timetable || $timetable['type'] !== 'TIMETABLE') {
            return $this->error('Timetable not found.', [], 404);
        }

        $this->repository->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_TIMETABLE', "Deleted timetable entry ID: $id");
        return $this->success('Timetable deleted successfully');
    }
}
