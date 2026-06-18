<?php
namespace PharmacyErpApi\Repositories;
class MedicineRepository extends BaseRepository {
    public function __construct() { parent::__construct('medicines'); }
    public function findByCode(string $code): ?array {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE medicine_code=%s AND deleted_at IS NULL", $code), ARRAY_A) ?: null;
    }
    public function getLowStock(): array {
        global $wpdb;
        $p = $wpdb->prefix;
        return $wpdb->get_results("SELECT m.*, COALESCE(SUM(b.available_qty),0) as current_stock FROM {$this->table_name} m LEFT JOIN {$p}pharmacy_batches b ON b.medicine_id=m.id AND b.available_qty>0 AND b.expiry_date>=CURDATE() WHERE m.deleted_at IS NULL GROUP BY m.id HAVING current_stock <= m.reorder_level ORDER BY current_stock ASC", ARRAY_A) ?: [];
    }
    public function getStockSummary(int $id): array {
        global $wpdb;
        $p = $wpdb->prefix;
        $stock = (int)$wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(available_qty),0) FROM {$p}pharmacy_batches WHERE medicine_id=%d AND available_qty>0 AND expiry_date>=CURDATE()", $id));
        $expired = (int)$wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(available_qty),0) FROM {$p}pharmacy_batches WHERE medicine_id=%d AND expiry_date<CURDATE()", $id));
        return ['current_stock' => $stock, 'expired_stock' => $expired];
    }
}
