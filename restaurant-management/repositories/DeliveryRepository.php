<?php
namespace RestaurantManagementApi\Repositories;

class DeliveryRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_deliveries';
    }

    public function findByOrderId(int $order_id) {
        $query = $this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE order_id = %d", $order_id);
        return $this->wpdb->get_row($query, ARRAY_A);
    }
}
