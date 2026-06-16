<?php
namespace ManufacturingManagementApi\Repositories;

class InventoryRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_inventory';
    }

    public function logMovement(string $item_type, int $item_id, string $movement_type, float $quantity, string $reference = '') {
        return $this->create([
            'item_type' => $item_type,
            'item_id' => $item_id,
            'movement_type' => $movement_type,
            'quantity' => $quantity,
            'reference' => $reference,
            'created_at' => current_time('mysql')
        ]);
    }

    public function getLowStockRawMaterials() {
        $raw_table = $this->wpdb->prefix . 'mfg_raw_materials';
        return $this->wpdb->get_results(
            "SELECT * FROM $raw_table WHERE current_stock <= minimum_stock AND status = 'ACTIVE' ORDER BY material_name ASC",
            ARRAY_A
        );
    }
}
