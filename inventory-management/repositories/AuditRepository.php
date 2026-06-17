<?php
namespace InventoryManagementApi\Repositories;

class AuditRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('audits', true);
    }

    public function existsAuditNumber(string $num, ?int $exclude_id = null): bool {
        return $this->exists('audit_number', $num, $exclude_id);
    }

    /**
     * Get items associated with an Audit
     */
    public function getAuditItems(int $audit_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_audit_items';
        $products_table = $wpdb->prefix . 'inv_products';
        
        $query = "
            SELECT ai.*, p.product_name, p.sku, p.unit 
            FROM $table ai
            JOIN $products_table p ON ai.product_id = p.id
            WHERE ai.audit_id = %d
        ";
        return $wpdb->get_results($wpdb->prepare($query, $audit_id), ARRAY_A) ?: [];
    }

    /**
     * Add items to an Audit
     */
    public function addAuditItems(int $audit_id, array $items): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'inv_audit_items';
        
        foreach ($items as $item) {
            $variance = (int)$item['physical_quantity'] - (int)$item['system_quantity'];
            $result = $wpdb->insert(
                $table,
                [
                    'audit_id' => $audit_id,
                    'product_id' => $item['product_id'],
                    'system_quantity' => $item['system_quantity'],
                    'physical_quantity' => $item['physical_quantity'],
                    'variance' => $variance
                ],
                ['%d', '%d', '%d', '%d', '%d']
            );
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
