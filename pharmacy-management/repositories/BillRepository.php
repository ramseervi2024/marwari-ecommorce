<?php
namespace PharmacyErpApi\Repositories;
class BillRepository extends BaseRepository {
    public function __construct() { parent::__construct('bills'); }
    public function getItems(int $billId): array {
        global $wpdb;
        $p = $wpdb->prefix;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT bi.*, m.name as medicine_name, m.medicine_code, m.unit FROM {$p}pharmacy_bill_items bi LEFT JOIN {$p}pharmacy_medicines m ON m.id=bi.medicine_id WHERE bi.bill_id=%d",
            $billId
        ), ARRAY_A) ?: [];
    }
    public function deleteItems(int $billId): void {
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'pharmacy_bill_items', ['bill_id' => $billId]);
    }
    public function insertItem(array $item): ?int {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'pharmacy_bill_items', $item);
        return $wpdb->insert_id ?: null;
    }
    public function getDailyRevenue(): array {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT DATE(created_at) as date, SUM(grand_total) as revenue, COUNT(*) as bills FROM {$this->table_name} WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY date ASC",
            ARRAY_A
        ) ?: [];
    }
}
