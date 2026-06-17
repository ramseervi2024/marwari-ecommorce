<?php
namespace JewelleryManagementApi\Repositories;

class InventoryRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_inventory';
    }

    public function getByBarcode(string $barcode) {
        $query = $this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE barcode = %s AND status = 'ACTIVE'", $barcode);
        return $this->wpdb->get_row($query, ARRAY_A);
    }
}
