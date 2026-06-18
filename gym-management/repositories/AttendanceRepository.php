<?php
namespace GymErpApi\Repositories;
class AttendanceRepository extends BaseRepository {
    public function __construct() { parent::__construct('attendance', false); }
    public function markAttendance(string $userType, int $refId): array {
        global $wpdb;
        // Check if already checked in today
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE user_type=%s AND reference_id=%d AND DATE(check_in) = CURDATE()",
            $userType, $refId
        ));
        if ($existing) {
            if (!$existing->check_out) {
                $wpdb->update($this->table_name, ['check_out' => current_time('mysql')], ['id' => $existing->id]);
                return ['success' => true, 'message' => 'Checked out successfully.'];
            }
            return ['success' => false, 'message' => 'Already checked out today.'];
        }
        $wpdb->insert($this->table_name, [
            'user_type' => $userType,
            'reference_id' => $refId,
            'check_in' => current_time('mysql')
        ]);
        return ['success' => true, 'message' => 'Checked in successfully.'];
    }
}
