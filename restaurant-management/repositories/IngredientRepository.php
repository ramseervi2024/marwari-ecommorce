<?php
namespace RestaurantManagementApi\Repositories;

class IngredientRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_ingredients';
    }
}
