<?php
namespace GymErpApi\Controllers;
use WP_REST_Request;

class DashboardController extends BaseController {
    public function getStats(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        
        $total_members = $wpdb->get_var("SELECT COUNT(*) FROM {$p}gym_members WHERE status='Active' AND deleted_at IS NULL");
        $total_trainers = $wpdb->get_var("SELECT COUNT(*) FROM {$p}gym_trainers WHERE status='Active' AND deleted_at IS NULL");
        $attendance_today = $wpdb->get_var("SELECT COUNT(*) FROM {$p}gym_attendance WHERE DATE(check_in) = CURDATE()");
        $revenue_today = $wpdb->get_var("SELECT COALESCE(SUM(amount),0) FROM {$p}gym_payments WHERE payment_date = CURDATE() AND deleted_at IS NULL");
        
        $expiring = $wpdb->get_results("SELECT ms.*, m.name as member_name, pl.name as plan_name 
            FROM {$p}gym_memberships ms JOIN {$p}gym_members m ON m.id=ms.member_id JOIN {$p}gym_plans pl ON pl.id=ms.plan_id 
            WHERE ms.status='Active' AND ms.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND ms.deleted_at IS NULL", ARRAY_A);
            
        $recent_payments = $wpdb->get_results("SELECT p.*, m.name as member_name FROM {$p}gym_payments p JOIN {$p}gym_members m ON m.id=p.member_id WHERE p.deleted_at IS NULL ORDER BY p.id DESC LIMIT 5", ARRAY_A);

        return $this->success('Dashboard stats.', [
            'summary' => [
                'total_members' => (int)$total_members,
                'total_trainers' => (int)$total_trainers,
                'attendance_today' => (int)$attendance_today,
                'revenue_today' => (float)$revenue_today
            ],
            'expiring_soon' => $expiring ?: [],
            'recent_payments' => $recent_payments ?: []
        ]);
    }
}
