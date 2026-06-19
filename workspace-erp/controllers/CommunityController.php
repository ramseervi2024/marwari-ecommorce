<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\AnnouncementRepository;
use WorkspaceErpApi\Repositories\EventRepository;
use WorkspaceErpApi\Repositories\ServiceRequestRepository;
use WP_REST_Request;

class CommunityController extends BaseController {
    private $annRepo;
    private $eventRepo;
    private $reqRepo;

    public function __construct() {
        $this->annRepo = new AnnouncementRepository();
        $this->eventRepo = new EventRepository();
        $this->reqRepo = new ServiceRequestRepository();
    }

    public function indexAnnouncements(WP_REST_Request $request) {
        return $this->success('Announcements fetched successfully', $this->annRepo->findAll($request->get_params(), ['id', 'title'], ['title', 'description']));
    }

    public function createAnnouncement(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['title'])) return $this->error('title is required.');

        $data = [
            'title' => sanitize_text_field($params['title']),
            'description' => isset($params['description']) ? sanitize_textarea_field($params['description']) : '',
            'target_audience' => 'ALL',
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->annRepo->create($data, ['%s', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Announcement published successfully', array_merge(['id' => $id], $data), 201);
    }

    public function indexEvents(WP_REST_Request $request) {
        return $this->success('Events fetched successfully', $this->eventRepo->findAll($request->get_params(), ['id', 'title'], ['title', 'description']));
    }

    public function createEvent(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['title']) || empty($params['event_date'])) {
            return $this->error('title and event_date are required.');
        }

        $data = [
            'title' => sanitize_text_field($params['title']),
            'description' => isset($params['description']) ? sanitize_textarea_field($params['description']) : '',
            'event_date' => sanitize_text_field($params['event_date']),
            'location' => isset($params['location']) ? sanitize_text_field($params['location']) : 'Rooftop Lounge',
            'organizer' => 'Community Management',
            'status' => 'UPCOMING',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->eventRepo->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Event scheduled successfully', array_merge(['id' => $id], $data), 201);
    }

    public function indexServiceRequests(WP_REST_Request $request) {
        return $this->success('Service requests fetched successfully', $this->reqRepo->findAll($request->get_params(), ['id', 'request_no', 'status'], ['request_no', 'description']));
    }
}
