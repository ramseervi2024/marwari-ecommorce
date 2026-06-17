<?php
namespace ServiceManagementApi\Repositories;

class QuotationRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('quotations', true);
    }

    public function existsQuotationNumber(string $num, ?int $exclude_id = null): bool {
        return $this->exists('quotation_number', $num, $exclude_id);
    }

    /**
     * Get items associated with a Quotation
     */
    public function getQuotationItems(int $quotation_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'ser_quotation_items';
        
        $query = "SELECT * FROM $table WHERE quotation_id = %d";
        return $wpdb->get_results($wpdb->prepare($query, $quotation_id), ARRAY_A) ?: [];
    }

    /**
     * Add items to a Quotation
     */
    public function addQuotationItems(int $quotation_id, array $items): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'ser_quotation_items';
        
        foreach ($items as $item) {
            $result = $wpdb->insert(
                $table,
                [
                    'quotation_id' => $quotation_id,
                    'service_name' => sanitize_text_field($item['service_name']),
                    'quantity' => intval($item['quantity'] ?? 1),
                    'price' => floatval($item['price'] ?? 0.00)
                ],
                ['%d', '%s', '%d', '%f']
            );
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
}
