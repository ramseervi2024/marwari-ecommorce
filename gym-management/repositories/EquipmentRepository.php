<?php
namespace GymErpApi\Repositories;
class EquipmentRepository extends BaseRepository {
    public function __construct() { parent::__construct('equipment'); }

    public function getSummary(): array {
        global $wpdb;
        $t = $this->table_name;
        $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM $t WHERE deleted_at IS NULL");
        $good = (int)$wpdb->get_var("SELECT COUNT(*) FROM $t WHERE deleted_at IS NULL AND condition_status='Good'");
        $needs_repair = (int)$wpdb->get_var("SELECT COUNT(*) FROM $t WHERE deleted_at IS NULL AND condition_status='Needs Repair'");
        $out_of_order = (int)$wpdb->get_var("SELECT COUNT(*) FROM $t WHERE deleted_at IS NULL AND condition_status='Out of Order'");
        $maint_due = (int)$wpdb->get_var("SELECT COUNT(*) FROM $t WHERE deleted_at IS NULL AND next_maintenance_date IS NOT NULL AND next_maintenance_date <= CURDATE()");
        return compact('total', 'good', 'needs_repair', 'out_of_order', 'maint_due');
    }
}
