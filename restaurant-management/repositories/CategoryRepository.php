<?php
namespace RestaurantManagementApi\Repositories;

class CategoryRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_categories';
    }
}
