<?php
namespace GymErpApi\Repositories;
class PaymentRepository extends BaseRepository {
    public function __construct() { parent::__construct('payments'); }
    public function getDailyRevenue(): array {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT DATE(payment_date) as date, SUM(amount) as revenue, COUNT(*) as tx_count 
             FROM {$this->table_name} 
             WHERE deleted_at IS NULL AND payment_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
             GROUP BY DATE(payment_date) ORDER BY date ASC",
            ARRAY_A
        ) ?: [];
    }
}
