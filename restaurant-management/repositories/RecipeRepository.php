<?php
namespace RestaurantManagementApi\Repositories;

class RecipeRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_recipes';
    }

    public function findByMenuItem(int $menu_item_id) {
        $table_ing = $this->wpdb->prefix . 'restaurant_ingredients';
        $query = $this->wpdb->prepare(
            "SELECT r.*, i.ingredient_name, i.unit, i.purchase_price 
             FROM {$this->table_name} r
             JOIN {$table_ing} i ON r.ingredient_id = i.id 
             WHERE r.menu_item_id = %d", 
            $menu_item_id
        );
        return $this->wpdb->get_results($query, ARRAY_A);
    }

    public function deleteByMenuItem(int $menu_item_id) {
        return $this->wpdb->delete($this->table_name, ['menu_item_id' => $menu_item_id]) !== false;
    }
}
