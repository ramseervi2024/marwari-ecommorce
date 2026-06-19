<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\RoomBookingRepository;
use WorkspaceErpApi\Repositories\VisitorRepository;
use WorkspaceErpApi\Repositories\InvoiceRepository;
use WorkspaceErpApi\Repositories\ServiceRequestRepository;
use WP_REST_Request;

class MobileController extends BaseController {

    public function getDashboard(WP_REST_Request $request) {
        return $this->success('Mobile dashboard metrics fetched', [
            'notifications_count' => 3,
            'active_bookings' => 1,
            'pending_visitors' => 1,
            'outstanding_invoices_sum' => 250750.00
        ]);
    }

    public function getBookings(WP_REST_Request $request) {
        $repo = new RoomBookingRepository();
        return $this->success('Bookings fetched', $repo->findAll($request->get_params()));
    }

    public function getVisitors(WP_REST_Request $request) {
        $repo = new VisitorRepository();
        return $this->success('Visitors fetched', $repo->findAll($request->get_params()));
    }

    public function getInvoices(WP_REST_Request $request) {
        $repo = new InvoiceRepository();
        return $this->success('Invoices fetched', $repo->findAll($request->get_params()));
    }

    public function createServiceRequest(WP_REST_Request $request) {
        $repo = new ServiceRequestRepository();
        $params = $request->get_json_params();
        if (empty($params['request_type'])) return $this->error('request_type is required.');

        $no = 'REQ-' . rand(1000, 9999);
        $data = [
            'request_no' => $no,
            'request_type' => sanitize_text_field($params['request_type']),
            'description' => isset($params['description']) ? sanitize_textarea_field($params['description']) : '',
            'status' => 'OPEN',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $repo->create($data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Service request raised via mobile', array_merge(['id' => $id], $data), 201);
    }

    public function createMeetingRoomBooking(WP_REST_Request $request) {
        $repo = new RoomBookingRepository();
        $params = $request->get_json_params();
        if (empty($params['room_id']) || empty($params['booking_date'])) return $this->error('room_id and booking_date are required.');

        $data = [
            'room_id' => intval($params['room_id']),
            'booked_by' => get_current_user_id(),
            'booking_date' => sanitize_text_field($params['booking_date']),
            'start_time' => isset($params['start_time']) ? sanitize_text_field($params['start_time']) : '10:00:00',
            'end_time' => isset($params['end_time']) ? sanitize_text_field($params['end_time']) : '11:00:00',
            'status' => 'CONFIRMED',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $repo->create($data, ['%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
        return $this->success('Meeting Room booked via mobile', array_merge(['id' => $id], $data), 201);
    }
}
