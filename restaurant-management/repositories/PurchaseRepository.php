<?php
namespace RestaurantManagementApi\Repositories;

class PurchaseRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_purchases';
    }

    public function saveItems(int $purchase_id, array $items) {
        $t_items = $this->wpdb->prefix . 'restaurant_purchase_items';
        $t_ingredients = $this->wpdb->prefix . 'restaurant_ingredients';
        
        foreach ($items as $item) {
            $this->wpdb->insert($t_items, [
                'purchase_id' => $purchase_id,
                'ingredient_id' => intval($item['ingredient_id']),
                'quantity' => floatval($item['quantity']),
                'price' => floatval($item['price']),
                'total' => floatval($item['quantity']) * floatval($item['price'])
            ]);

            // Auto replenish ingredient stock
            $this->wpdb->query($this->wpdb->prepare(
                "UPDATE {$t_ingredients} 
                 SET current_stock = current_stock + %f 
                 WHERE id = %d",
                floatval($item['quantity']),
                intval($item['ingredient_id'])
            ));
        }
        return true;
    }

    public function getItems(int $purchase_id) {
        $t_items = $this->wpdb->prefix . 'restaurant_purchase_items';
        $t_ingredients = $this->wpdb->prefix . 'restaurant_ingredients';
        $query = $this->wpdb->prepare(
            "SELECT pi.*, i.ingredient_name, i.unit 
             FROM {$t_items} pi
             JOIN {$t_ingredients} i ON pi.ingredient_id = i.id 
             WHERE pi.purchase_id = %d",
            $purchase_id
        );
        return $this->wpdb->get_results($query, ARRAY_A);
    }
}
