<?php
namespace RestaurantManagementApi\Repositories;

class MenuRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_menu';
    }

    public function findByCode(string $code) {
        $query = $this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE item_code = %s", $code);
        return $this->wpdb->get_row($query, ARRAY_A);
    }
}
