<?php
namespace GarmentManagementApi\Repositories;

class BomRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_bom';
    }

    public function getByProduct(string $product_id) {
        $query = $this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE product_id = %s", $product_id);
        return $this->wpdb->get_results($query, ARRAY_A);
    }
}
