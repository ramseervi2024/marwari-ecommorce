<?php
namespace GarmentManagementApi\Repositories;

class InventoryRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_inventory';
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

    public function getLowStockFabrics() {
        $fab_table = $this->wpdb->prefix . 'garment_fabrics';
        return $this->wpdb->get_results(
            "SELECT * FROM $fab_table WHERE available_meters <= 50 AND status = 'ACTIVE' ORDER BY fabric_name ASC",
            ARRAY_A
        );
    }
}
