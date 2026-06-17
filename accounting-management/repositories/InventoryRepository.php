<?php
namespace AccountingManagementApi\Repositories;

class InventoryRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('inventory', false);
    }

    public function findByItemId(int $item_id): ?array {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE item_id = %d", $item_id),
            ARRAY_A
        );
        return $row ?: null;
    }
}
