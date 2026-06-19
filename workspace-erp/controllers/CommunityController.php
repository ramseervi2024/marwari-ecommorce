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

    public function updateAnnouncement(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $ann = $this->annRepo->findById($id);
        if (!$ann) return $this->error('Announcement not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['title'])) { $update['title'] = sanitize_text_field($params['title']); $formats[] = '%s'; }
        if (isset($params['description'])) { $update['description'] = sanitize_textarea_field($params['description']); $formats[] = '%s'; }
        if (isset($params['target_audience'])) { $update['target_audience'] = sanitize_text_field($params['target_audience']); $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');
        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->annRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update announcement.');

        return $this->success('Announcement updated successfully', $this->annRepo->findById($id));
    }

    public function deleteAnnouncement(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $ann = $this->annRepo->findById($id);
        if (!$ann) return $this->error('Announcement not found.', [], 404);

        $this->annRepo->delete($id);
        return $this->success('Announcement deleted successfully');
    }

    public function updateEvent(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $event = $this->eventRepo->findById($id);
        if (!$event) return $this->error('Event not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['title'])) { $update['title'] = sanitize_text_field($params['title']); $formats[] = '%s'; }
        if (isset($params['description'])) { $update['description'] = sanitize_textarea_field($params['description']); $formats[] = '%s'; }
        if (isset($params['event_date'])) { $update['event_date'] = sanitize_text_field($params['event_date']); $formats[] = '%s'; }
        if (isset($params['location'])) { $update['location'] = sanitize_text_field($params['location']); $formats[] = '%s'; }
        if (isset($params['organizer'])) { $update['organizer'] = sanitize_text_field($params['organizer']); $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');
        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->eventRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update event.');

        return $this->success('Event updated successfully', $this->eventRepo->findById($id));
    }

    public function deleteEvent(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $event = $this->eventRepo->findById($id);
        if (!$event) return $this->error('Event not found.', [], 404);

        $this->eventRepo->delete($id);
        return $this->success('Event deleted successfully');
    }
}
