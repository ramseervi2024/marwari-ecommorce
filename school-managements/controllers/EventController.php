<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\EventRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class EventController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new EventRepository();
    }

    /**
     * GET /events
     */
    public function getEvents(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = ['type' => 'EVENT'];
        $result = $this->repository->findAll($params, ['id', 'title', 'event_date', 'status'], ['title', 'description'], $filters);
        return $this->success('School events fetched successfully', $result);
    }

    /**
     * POST /events
     */
    public function createEvent(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['title']) || empty($params['event_date'])) {
            return $this->error('Validation failed: title and event_date are required.');
        }

        $data = [
            'type' => 'EVENT',
            'title' => sanitize_text_field($params['title']),
            'description' => isset($params['description']) ? sanitize_textarea_field($params['description']) : null,
            'event_date' => sanitize_text_field($params['event_date']),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) {
            return $this->error('Failed to create school event.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_EVENT', "Created school event: {$params['title']}");
        return $this->success('School event created successfully', array_merge(['id' => $id], $data), 201);
    }

    /**
     * PUT /events/{id}
     */
    public function updateEvent(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $event = $this->repository->findById($id);

        if (!$event || $event['type'] !== 'EVENT') {
            return $this->error('Event not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'title' => '%s',
            'description' => '%s',
            'event_date' => '%s',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                $data[$field] = $field === 'description' ? sanitize_textarea_field($params[$field]) : sanitize_text_field($params[$field]);
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->repository->update($id, $data, $formats);
        return $this->success('School event updated successfully', $this->repository->findById($id));
    }

    /**
     * DELETE /events/{id}
     */
    public function deleteEvent(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $event = $this->repository->findById($id);

        if (!$event || $event['type'] !== 'EVENT') {
            return $this->error('Event not found.', [], 404);
        }

        $this->repository->delete($id);
        return $this->success('School event deleted successfully');
    }

    /**
     * GET /notices
     */
    public function getNotices(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = ['type' => 'NOTICE'];
        $result = $this->repository->findAll($params, ['id', 'title', 'created_at', 'status'], ['title', 'description'], $filters);
        return $this->success('Notice board notices fetched successfully', $result);
    }

    /**
     * POST /notices
     */
    public function createNotice(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['title']) || empty($params['description'])) {
            return $this->error('Validation failed: title and description are required.');
        }

        $data = [
            'type' => 'NOTICE',
            'title' => sanitize_text_field($params['title']),
            'description' => sanitize_textarea_field($params['description']),
            'event_date' => null,
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) {
            return $this->error('Failed to create notice.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_NOTICE', "Posted notice to Notice Board: {$params['title']}");
        return $this->success('Notice posted successfully', array_merge(['id' => $id], $data), 201);
    }
}
