<?php
namespace PharmacyErpApi\Repositories;
class BatchRepository extends BaseRepository {
    public function __construct() { parent::__construct('batches', false); }
    public function getExpiryAlerts(int $days = 30): array {
        global $wpdb;
        $p = $wpdb->prefix;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT b.*, m.name as medicine_name, m.medicine_code FROM {$this->table_name} b JOIN {$p}pharmacy_medicines m ON m.id=b.medicine_id WHERE b.available_qty>0 AND b.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL %d DAY) ORDER BY b.expiry_date ASC",
            $days
        ), ARRAY_A) ?: [];
    }
    public function getExpired(): array {
        global $wpdb;
        $p = $wpdb->prefix;
        return $wpdb->get_results(
            "SELECT b.*, m.name as medicine_name, m.medicine_code FROM {$this->table_name} b JOIN {$p}pharmacy_medicines m ON m.id=b.medicine_id WHERE b.expiry_date < CURDATE() AND b.available_qty > 0 ORDER BY b.expiry_date ASC",
            ARRAY_A
        ) ?: [];
    }
    public function getByMedicine(int $medicineId): array {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE medicine_id=%d ORDER BY expiry_date ASC", $medicineId), ARRAY_A) ?: [];
    }
    public function getAvailableByMedicine(int $medicineId): array {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE medicine_id=%d AND available_qty>0 AND expiry_date>=CURDATE() ORDER BY expiry_date ASC", $medicineId), ARRAY_A) ?: [];
    }
    public function deductStock(int $batchId, int $qty): bool {
        global $wpdb;
        return $wpdb->query($wpdb->prepare("UPDATE {$this->table_name} SET available_qty=GREATEST(0,available_qty-%d) WHERE id=%d", $qty, $batchId)) !== false;
    }
    public function addStock(int $batchId, int $qty): bool {
        global $wpdb;
        return $wpdb->query($wpdb->prepare("UPDATE {$this->table_name} SET available_qty=available_qty+%d WHERE id=%d", $qty, $batchId)) !== false;
    }
}
