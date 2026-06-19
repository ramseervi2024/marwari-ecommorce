<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\BuildingRepository;
use WorkspaceErpApi\Repositories\FloorRepository;
use WorkspaceErpApi\Repositories\WorkspaceRepository;
use WorkspaceErpApi\Repositories\SeatRepository;
use WorkspaceErpApi\Repositories\MeetingRoomRepository;
use WorkspaceErpApi\Repositories\RoomBookingRepository;
use WorkspaceErpApi\Services\AuthService;
use WP_REST_Request;

class WorkspaceController extends BaseController {
    private $buildingRepo;
    private $floorRepo;
    private $workspaceRepo;
    private $seatRepo;
    private $roomRepo;
    private $bookingRepo;

    public function __construct() {
        $this->buildingRepo = new BuildingRepository();
        $this->floorRepo = new FloorRepository();
        $this->workspaceRepo = new WorkspaceRepository();
        $this->seatRepo = new SeatRepository();
        $this->roomRepo = new MeetingRoomRepository();
        $this->bookingRepo = new RoomBookingRepository();
    }

    public function indexBuildings(WP_REST_Request $request) {
        return $this->success('Buildings fetched successfully', $this->buildingRepo->findAll($request->get_params(), ['id', 'building_name', 'city'], ['building_name', 'city']));
    }

    public function createBuilding(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['building_name'])) return $this->error('building_name is required.');

        $data = [
            'building_name' => sanitize_text_field($params['building_name']),
            'address' => isset($params['address']) ? sanitize_textarea_field($params['address']) : '',
            'city' => isset($params['city']) ? sanitize_text_field($params['city']) : 'Bangalore',
            'state' => isset($params['state']) ? sanitize_text_field($params['state']) : 'Karnataka',
            'total_floors' => isset($params['total_floors']) ? intval($params['total_floors']) : 1,
            'total_seats' => isset($params['total_seats']) ? intval($params['total_seats']) : 0,
            'amenities' => isset($params['amenities']) ? sanitize_text_field($params['amenities']) : '',
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->buildingRepo->create($data, ['%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s']);
        return $this->success('Building created successfully', array_merge(['id' => $id], $data), 201);
    }

    public function indexFloors(WP_REST_Request $request) {
        return $this->success('Floors fetched successfully', $this->floorRepo->findAll($request->get_params(), ['id', 'floor_name'], ['floor_name']));
    }

    public function indexWorkspaces(WP_REST_Request $request) {
        return $this->success('Workspaces fetched successfully', $this->workspaceRepo->findAll($request->get_params(), ['id', 'workspace_name'], ['workspace_name']));
    }

    public function createWorkspace(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['workspace_name']) || empty($params['building_id']) || empty($params['floor_id'])) {
            return $this->error('workspace_name, building_id, and floor_id are required.');
        }

        $data = [
            'building_id' => intval($params['building_id']),
            'floor_id' => intval($params['floor_id']),
            'workspace_name' => sanitize_text_field($params['workspace_name']),
            'workspace_type' => isset($params['workspace_type']) ? sanitize_text_field($params['workspace_type']) : 'CABIN',
            'capacity' => isset($params['capacity']) ? intval($params['capacity']) : 1,
            'rate_per_seat' => isset($params['rate_per_seat']) ? floatval($params['rate_per_seat']) : 0.00,
            'status' => 'AVAILABLE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->workspaceRepo->create($data, ['%d', '%d', '%s', '%s', '%d', '%f', '%s', '%s', '%s']);
        return $this->success('Workspace created successfully', array_merge(['id' => $id], $data), 201);
    }

    public function indexSeats(WP_REST_Request $request) {
        return $this->success('Seats fetched successfully', $this->seatRepo->findAll($request->get_params(), ['id', 'seat_number', 'status'], ['seat_number']));
    }

    public function indexMeetingRooms(WP_REST_Request $request) {
        return $this->success('Meeting Rooms fetched successfully', $this->roomRepo->findAll($request->get_params(), ['id', 'room_name', 'status'], ['room_name']));
    }

    public function indexBookings(WP_REST_Request $request) {
        return $this->success('Bookings fetched successfully', $this->bookingRepo->findAll($request->get_params(), ['id', 'booking_date'], []));
    }

    public function createBooking(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['room_id']) || empty($params['booking_date']) || empty($params['start_time']) || empty($params['end_time'])) {
            return $this->error('room_id, booking_date, start_time, and end_time are required.');
        }

        $data = [
            'room_id' => intval($params['room_id']),
            'client_id' => isset($params['client_id']) ? intval($params['client_id']) : null,
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
        $id = $this->bookingRepo->create($data, ['%d', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s']);
        return $this->success('Meeting Room booked successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateBuilding(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $building = $this->buildingRepo->findById($id);
        if (!$building) return $this->error('Building not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['building_name'])) { $update['building_name'] = sanitize_text_field($params['building_name']); $formats[] = '%s'; }
        if (isset($params['address'])) { $update['address'] = sanitize_textarea_field($params['address']); $formats[] = '%s'; }
        if (isset($params['city'])) { $update['city'] = sanitize_text_field($params['city']); $formats[] = '%s'; }
        if (isset($params['state'])) { $update['state'] = sanitize_text_field($params['state']); $formats[] = '%s'; }
        if (isset($params['total_floors'])) { $update['total_floors'] = intval($params['total_floors']); $formats[] = '%d'; }
        if (isset($params['total_seats'])) { $update['total_seats'] = intval($params['total_seats']); $formats[] = '%d'; }
        if (isset($params['amenities'])) { $update['amenities'] = sanitize_text_field($params['amenities']); $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->buildingRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update building.');

        AuthService::logActivity(get_current_user_id(), 'UPDATE_BUILDING', "Updated building ID: $id");
        return $this->success('Building updated successfully', $this->buildingRepo->findById($id));
    }

    public function deleteBuilding(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $building = $this->buildingRepo->findById($id);
        if (!$building) return $this->error('Building not found.', [], 404);

        $this->buildingRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_BUILDING', "Soft deleted building ID: $id");
        return $this->success('Building deleted successfully');
    }

    public function updateBooking(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $booking = $this->bookingRepo->findById($id);
        if (!$booking) return $this->error('Booking not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['room_id'])) { $update['room_id'] = intval($params['room_id']); $formats[] = '%d'; }
        if (isset($params['booking_date'])) { $update['booking_date'] = sanitize_text_field($params['booking_date']); $formats[] = '%s'; }
        if (isset($params['start_time'])) { $update['start_time'] = sanitize_text_field($params['start_time']); $formats[] = '%s'; }
        if (isset($params['end_time'])) { $update['end_time'] = sanitize_text_field($params['end_time']); $formats[] = '%s'; }
        if (isset($params['purpose'])) { $update['purpose'] = sanitize_text_field($params['purpose']); $formats[] = '%s'; }
        if (isset($params['attendees'])) { $update['attendees'] = intval($params['attendees']); $formats[] = '%d'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->bookingRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update booking.');

        AuthService::logActivity(get_current_user_id(), 'UPDATE_BOOKING', "Updated booking ID: $id");
        return $this->success('Booking updated successfully', $this->bookingRepo->findById($id));
    }

    public function deleteBooking(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $booking = $this->bookingRepo->findById($id);
        if (!$booking) return $this->error('Booking not found.', [], 404);

        $this->bookingRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_BOOKING', "Soft deleted booking ID: $id");
        return $this->success('Booking deleted successfully');
    }
}
