<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\EventRepository;
use SchoolManagementApi\Repositories\DocumentRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class HomeworkController extends BaseController {
    private $eventRepo;
    private $documentRepo;

    public function __construct() {
        $this->eventRepo = new EventRepository();
        $this->documentRepo = new DocumentRepository();
    }

    /**
     * GET /homework
     */
    public function index(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = ['type' => 'HOMEWORK'];
        if (!empty($params['class_id'])) {
            $filters['description'] = '%' . $params['class_id'] . '%';
        }
        
        $result = $this->eventRepo->findAll($params, ['id', 'title'], [], $filters);
        
        // Decode details
        foreach ($result['data'] as &$row) {
            $row['details'] = json_decode($row['description'], true);
        }
        
        return $this->success('Homework entries fetched successfully', $result);
    }

    /**
     * POST /homework
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['class_id']) || empty($params['subject_id']) || empty($params['title']) || empty($params['due_date'])) {
            return $this->error('Validation failed: class_id, subject_id, title, and due_date are required.');
        }

        $description = json_encode([
            'class_id' => (int)$params['class_id'],
            'subject_id' => (int)$params['subject_id'],
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'due_date' => sanitize_text_field($params['due_date'])
        ]);

        $data = [
            'type' => 'HOMEWORK',
            'title' => sanitize_text_field($params['title']),
            'description' => $description,
            'event_date' => sanitize_text_field($params['due_date']),
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->eventRepo->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) {
            return $this->error('Failed to post homework.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_HOMEWORK', "Posted homework ID: $id");
        return $this->success('Homework created successfully', array_merge(['id' => $id], $data), 201);
    }

    /**
     * PUT /homework/{id}
     */
    public function update(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $homework = $this->eventRepo->findById($id);

        if (!$homework || $homework['type'] !== 'HOMEWORK') {
            return $this->error('Homework entry not found.', [], 404);
        }

        $params = $request->get_json_params();
        $current_details = json_decode($homework['description'], true) ?: [];

        if (isset($params['class_id'])) {
            $current_details['class_id'] = (int)$params['class_id'];
        }
        if (isset($params['subject_id'])) {
            $current_details['subject_id'] = (int)$params['subject_id'];
        }
        if (isset($params['description'])) {
            $current_details['description'] = sanitize_textarea_field($params['description']);
        }
        if (isset($params['due_date'])) {
            $current_details['due_date'] = sanitize_text_field($params['due_date']);
        }

        $data = [
            'title' => isset($params['title']) ? sanitize_text_field($params['title']) : $homework['title'],
            'description' => json_encode($current_details),
            'event_date' => $current_details['due_date'] ?? null,
            'updated_at' => current_time('mysql')
        ];

        $this->eventRepo->update($id, $data, ['%s', '%s', '%s', '%s']);
        
        return $this->success('Homework updated successfully', $this->eventRepo->findById($id));
    }

    /**
     * DELETE /homework/{id}
     */
    public function delete(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $homework = $this->eventRepo->findById($id);

        if (!$homework || $homework['type'] !== 'HOMEWORK') {
            return $this->error('Homework not found.', [], 404);
        }

        $this->eventRepo->delete($id);
        return $this->success('Homework deleted successfully');
    }

    /**
     * Student Homework Submission
     * POST /homework/submit
     */
    public function submit(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['homework_id']) || empty($params['student_id']) || empty($params['file_url'])) {
            return $this->error('Validation failed: homework_id, student_id, and file_url are required.');
        }

        $data = [
            'related_id' => (int)$params['student_id'],
            'related_type' => 'STUDENT',
            'document_type' => 'Homework Submission: ' . (int)$params['homework_id'],
            'file_url' => sanitize_text_field($params['file_url']),
            'media_id' => isset($params['media_id']) ? (int)$params['media_id'] : 0,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $doc_id = $this->documentRepo->create($data, ['%d', '%s', '%s', '%s', '%d', '%s', '%s']);
        if (!$doc_id) {
            return $this->error('Failed to submit homework.');
        }

        AuthService::logActivity(get_current_user_id(), 'SUBMIT_HOMEWORK', "Student ID: {$params['student_id']} submitted homework ID: {$params['homework_id']}");
        return $this->success('Homework submitted successfully', array_merge(['id' => $doc_id], $data), 201);
    }
}
