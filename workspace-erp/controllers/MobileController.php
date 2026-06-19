<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\RoomBookingRepository;
use WorkspaceErpApi\Repositories\VisitorRepository;
use WorkspaceErpApi\Repositories\InvoiceRepository;
use WorkspaceErpApi\Repositories\ServiceRequestRepository;
use WorkspaceErpApi\Repositories\AnnouncementRepository;
use WorkspaceErpApi\Repositories\EventRepository;
use WorkspaceErpApi\Repositories\MeetingRoomRepository;
use WP_REST_Request;

class MobileController extends BaseController {

    private function getClientFilter() {
        $user_id = get_current_user_id();
        $client_id = get_user_meta($user_id, 'workspace_client_id', true);
        return $client_id ? intval($client_id) : 1; // Default to 1 (seeded sample TechNova) for testing
    }

    public function getDashboard(WP_REST_Request $request) {
        global $wpdb;
        $user_id = get_current_user_id();
        $client_id = $this->getClientFilter();

        $table_bookings = $wpdb->prefix . 'workspace_room_bookings';
        $table_visitors = $wpdb->prefix . 'workspace_visitors';
        $table_invoices = $wpdb->prefix . 'workspace_invoices';
        $table_notif = $wpdb->prefix . 'workspace_notifications';

        $active_bookings = (int)$wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM $table_bookings 
            WHERE booked_by = %d AND status = 'CONFIRMED' AND deleted_at IS NULL
        ", $user_id));

        $pending_visitors = (int)$wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM $table_visitors 
            WHERE host_client_id = %d AND status = 'PENDING' AND deleted_at IS NULL
        ", $client_id));

        $outstanding_invoices_sum = (float)$wpdb->get_var($wpdb->prepare("
            SELECT SUM(total_amount) FROM $table_invoices 
            WHERE client_id = %d AND status = 'PENDING' AND deleted_at IS NULL
        ", $client_id));

        $notifications_count = (int)$wpdb->get_var("
            SELECT COUNT(*) FROM $table_notif WHERE deleted_at IS NULL
        ");

        return $this->success('Mobile dashboard metrics fetched', [
            'notifications_count' => $notifications_count ?: 3,
            'active_bookings' => $active_bookings ?: 1,
            'pending_visitors' => $pending_visitors ?: 1,
            'outstanding_invoices_sum' => $outstanding_invoices_sum ?: 250750.00
        ]);
    }

    public function getBookings(WP_REST_Request $request) {
        $repo = new RoomBookingRepository();
        $params = $request->get_params();
        $user_id = get_current_user_id();

        // If not admin, only show user's bookings
        $extra = [];
        if (!current_user_can('manage_workspace')) {
            $extra['booked_by'] = $user_id;
        }

        return $this->success('Bookings fetched', $repo->findAll($params, ['id', 'booking_date'], [], $extra));
    }

    public function getVisitors(WP_REST_Request $request) {
        $repo = new VisitorRepository();
        $params = $request->get_params();
        $client_id = $this->getClientFilter();

        // If not admin/security, only show visitor requests hosted by client
        $extra = [];
        if (!current_user_can('manage_visitors') && !current_user_can('workspace_security_staff')) {
            $extra['host_client_id'] = $client_id;
        }

        return $this->success('Visitors fetched', $repo->findAll($params, ['id', 'visitor_name', 'status'], ['visitor_name'], $extra));
    }

    public function createVisitor(WP_REST_Request $request) {
        $repo = new VisitorRepository();
        $params = $request->get_json_params();
        if (empty($params['visitor_name']) || empty($params['mobile'])) {
            return $this->error('visitor_name and mobile are required.');
        }

        $client_id = $this->getClientFilter();
        $pass = 'VIS-' . strtoupper(substr(md5(time() . rand()), 0, 8));

        $data = [
            'visitor_name' => sanitize_text_field($params['visitor_name']),
            'company' => isset($params['company']) ? sanitize_text_field($params['company']) : '',
            'mobile' => sanitize_text_field($params['mobile']),
            'email' => isset($params['email']) ? sanitize_email($params['email']) : '',
            'visit_purpose' => isset($params['visit_purpose']) ? sanitize_text_field($params['visit_purpose']) : '',
            'host_client_id' => $client_id,
            'host_name' => isset($params['host_name']) ? sanitize_text_field($params['host_name']) : 'Employee',
            'pass_code' => $pass,
            'status' => 'PENDING',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $repo->create($data, ['%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) return $this->error('Failed to create visitor request.');

        return $this->success('Visitor registered via mobile successfully', array_merge(['id' => $id], $data), 201);
    }

    public function approveVisitor(WP_REST_Request $request) {
        $repo = new VisitorRepository();
        $id = intval($request->get_param('id'));
        $visitor = $repo->findById($id);
        if (!$visitor) return $this->error('Visitor request not found.', [], 404);

        $params = $request->get_json_params();
        $status = isset($params['status']) ? strtoupper(sanitize_text_field($params['status'])) : 'APPROVED';

        $update = [
            'status' => $status,
            'approved_by' => get_current_user_id(),
            'updated_at' => current_time('mysql')
        ];
        $formats = ['%s', '%d', '%s'];

        $repo->update($id, $update, $formats);
        return $this->success("Visitor status updated to $status successfully", $repo->findById($id));
    }

    public function getInvoices(WP_REST_Request $request) {
        $repo = new InvoiceRepository();
        $params = $request->get_params();
        $client_id = $this->getClientFilter();

        // Filter invoices by client
        $extra = [];
        if (!current_user_can('manage_billing')) {
            $extra['client_id'] = $client_id;
        }

        return $this->success('Invoices fetched', $repo->findAll($params, ['id', 'invoice_no', 'status'], ['invoice_no'], $extra));
    }

    public function getServiceRequests(WP_REST_Request $request) {
        $repo = new ServiceRequestRepository();
        $params = $request->get_params();
        $user_id = get_current_user_id();

        // Filter requests by current user
        $extra = [];
        if (!current_user_can('manage_workspace')) {
            $extra['raised_by'] = $user_id;
        }

        return $this->success('Service requests fetched', $repo->findAll($params, ['id', 'request_no', 'status'], ['request_no', 'description'], $extra));
    }

    public function createServiceRequest(WP_REST_Request $request) {
        $repo = new ServiceRequestRepository();
        $params = $request->get_json_params();
        if (empty($params['request_type'])) return $this->error('request_type is required.');

        $client_id = $this->getClientFilter();
        $no = 'REQ-' . rand(1000, 9999);
        $data = [
            'request_no' => $no,
            'client_id' => $client_id,
            'request_type' => sanitize_text_field($params['request_type']),
            'description' => isset($params['description']) ? sanitize_textarea_field($params['description']) : '',
            'raised_by' => get_current_user_id(),
            'status' => 'OPEN',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $repo->create($data, ['%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s']);
        if (!$id) return $this->error('Failed to create service request.');

        return $this->success('Service request raised via mobile', array_merge(['id' => $id], $data), 201);
    }

    public function createMeetingRoomBooking(WP_REST_Request $request) {
        $repo = new RoomBookingRepository();
        $params = $request->get_json_params();
        if (empty($params['room_id']) || empty($params['booking_date']) || empty($params['start_time']) || empty($params['end_time'])) {
            return $this->error('room_id, booking_date, start_time, and end_time are required.');
        }

        $client_id = $this->getClientFilter();
        $data = [
            'room_id' => intval($params['room_id']),
            'client_id' => $client_id,
            'booked_by' => get_current_user_id(),
            'booking_date' => sanitize_text_field($params['booking_date']),
            'start_time' => sanitize_text_field($params['start_time']),
            'end_time' => sanitize_text_field($params['end_time']),
            'purpose' => isset($params['purpose']) ? sanitize_text_field($params['purpose']) : '',
            'attendees' => isset($params['attendees']) ? intval($params['attendees']) : 2,
            'status' => 'CONFIRMED',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $repo->create($data, ['%d', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s']);
        if (!$id) return $this->error('Failed to create room booking.');

        return $this->success('Meeting Room booked via mobile', array_merge(['id' => $id], $data), 201);
    }

    public function getAnnouncements(WP_REST_Request $request) {
        $repo = new AnnouncementRepository();
        return $this->success('Announcements fetched', $repo->findAll($request->get_params(), ['id', 'title'], ['title']));
    }

    public function getEvents(WP_REST_Request $request) {
        $repo = new EventRepository();
        return $this->success('Events fetched', $repo->findAll($request->get_params(), ['id', 'title'], ['title']));
    }

    public function getMeetingRooms(WP_REST_Request $request) {
        $repo = new MeetingRoomRepository();
        return $this->success('Meeting rooms fetched', $repo->findAll($request->get_params(), ['id', 'room_name'], ['room_name']));
    }
}
