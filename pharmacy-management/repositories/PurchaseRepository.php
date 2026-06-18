<?php
namespace PharmacyErpApi\Repositories;
class PurchaseRepository extends BaseRepository {
    public function __construct() { parent::__construct('purchases'); }
    public function getItems(int $purchaseId): array {
        global $wpdb;
        $p = $wpdb->prefix;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT pi.*, m.name as medicine_name, m.medicine_code FROM {$p}pharmacy_purchase_items pi LEFT JOIN {$p}pharmacy_medicines m ON m.id=pi.medicine_id WHERE pi.purchase_id=%d",
            $purchaseId
        ), ARRAY_A) ?: [];
    }
    public function deleteItems(int $purchaseId): void {
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'pharmacy_purchase_items', ['purchase_id' => $purchaseId]);
    }
    public function insertItem(array $item): ?int {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'pharmacy_purchase_items', $item);
        return $wpdb->insert_id ?: null;
    }
}
