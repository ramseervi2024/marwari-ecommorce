<?php
namespace GymErpApi\Repositories;
class MembershipRepository extends BaseRepository {
    public function __construct() { parent::__construct('memberships'); }
    
    public function getActiveMemberships(): array {
        global $wpdb;
        $p = $wpdb->prefix;
        return $wpdb->get_results(
            "SELECT ms.*, m.name as member_name, m.member_id as member_code, pl.name as plan_name 
             FROM {$this->table_name} ms 
             JOIN {$p}gym_members m ON m.id = ms.member_id 
             JOIN {$p}gym_plans pl ON pl.id = ms.plan_id 
             WHERE ms.status = 'Active' AND ms.end_date >= CURDATE() AND ms.deleted_at IS NULL", 
            ARRAY_A
        ) ?: [];
    }
    
    public function getExpiringSoon(int $days = 7): array {
        global $wpdb;
        $p = $wpdb->prefix;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT ms.*, m.name as member_name, m.member_id as member_code, pl.name as plan_name, m.mobile
             FROM {$this->table_name} ms 
             JOIN {$p}gym_members m ON m.id = ms.member_id 
             JOIN {$p}gym_plans pl ON pl.id = ms.plan_id 
             WHERE ms.status = 'Active' AND ms.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL %d DAY) 
             AND ms.deleted_at IS NULL ORDER BY ms.end_date ASC",
            $days
        ), ARRAY_A) ?: [];
    }

    public function expireOldMemberships(): void {
        global $wpdb;
        $wpdb->query("UPDATE {$this->table_name} SET status = 'Expired' WHERE status = 'Active' AND end_date < CURDATE() AND deleted_at IS NULL");
    }
}
