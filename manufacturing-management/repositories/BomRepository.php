<?php
namespace ManufacturingManagementApi\Repositories;

class BomRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_bom';
    }

    public function getByProduct(int $product_id) {
        $query = $this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE product_id = %d", $product_id);
        return $this->wpdb->get_results($query, ARRAY_A);
    }
}
