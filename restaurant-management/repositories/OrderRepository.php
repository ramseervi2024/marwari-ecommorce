<?php
namespace RestaurantManagementApi\Repositories;

class OrderRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_orders';
    }

    public function getItems(int $order_id) {
        $t_items = $this->wpdb->prefix . 'restaurant_order_items';
        $t_menu = $this->wpdb->prefix . 'restaurant_menu';
        $query = $this->wpdb->prepare(
            "SELECT oi.*, m.item_name, m.item_code 
             FROM {$t_items} oi
             JOIN {$t_menu} m ON oi.menu_item_id = m.id 
             WHERE oi.order_id = %d",
            $order_id
        );
        return $this->wpdb->get_results($query, ARRAY_A);
    }

    public function saveItems(int $order_id, array $items) {
        $t_items = $this->wpdb->prefix . 'restaurant_order_items';
        // Clear existing items first
        $this->wpdb->delete($t_items, ['order_id' => $order_id]);

        foreach ($items as $item) {
            $this->wpdb->insert($t_items, [
                'order_id' => $order_id,
                'menu_item_id' => intval($item['menu_item_id']),
                'quantity' => intval($item['quantity']),
                'price' => floatval($item['price']),
                'tax' => floatval($item['tax'] ?? 0),
                'total' => floatval($item['price']) * intval($item['quantity'])
            ]);
        }
        return true;
    }

    /**
     * Automatically deduct ingredient stock when an order is preparing or completed
     */
    public function deductInventory(int $order_id) {
        $items = $this->getItems($order_id);
        $t_recipes = $this->wpdb->prefix . 'restaurant_recipes';
        $t_ingredients = $this->wpdb->prefix . 'restaurant_ingredients';

        foreach ($items as $item) {
            $menu_item_id = intval($item['menu_item_id']);
            $quantity_ordered = intval($item['quantity']);

            // Fetch recipe for this item
            $recipe_query = $this->wpdb->prepare(
                "SELECT ingredient_id, quantity_required FROM {$t_recipes} WHERE menu_item_id = %d",
                $menu_item_id
            );
            $recipe_ingredients = $this->wpdb->get_results($recipe_query, ARRAY_A);

            foreach ($recipe_ingredients as $recipe) {
                $ingredient_id = intval($recipe['ingredient_id']);
                $qty_required = floatval($recipe['quantity_required']);
                $total_deduct = $qty_required * $quantity_ordered;

                // Deduct stock
                $this->wpdb->query($this->wpdb->prepare(
                    "UPDATE {$t_ingredients} 
                     SET current_stock = GREATEST(0.00, current_stock - %f) 
                     WHERE id = %d",
                    $total_deduct,
                    $ingredient_id
                ));
            }
        }
        return true;
    }
}
