<?php
namespace GymErpApi\Controllers;
use GymErpApi\Repositories\AttendanceRepository;
use WP_REST_Request;

class AttendanceController extends BaseController {
    private $repo;
    public function __construct() { $this->repo = new AttendanceRepository(); }

    public function getAttendance(WP_REST_Request $request) {
        return $this->success('Attendance.', $this->repo->findAll($request->get_params(), ['user_type']));
    }
    public function markAttendance(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['user_type']) || empty($p['reference_id'])) return $this->error('User Type and ID required.');
        $result = $this->repo->markAttendance($p['user_type'], $p['reference_id']);
        return $result['success'] ? $this->success($result['message']) : $this->error($result['message']);
    }
}
