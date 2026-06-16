<?php
namespace RestaurantManagementApi\Repositories;

class CustomerRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_customers';
    }

    public function findByMobile(string $mobile) {
        $query = $this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE mobile = %s", $mobile);
        return $this->wpdb->get_row($query, ARRAY_A);
    }
}
