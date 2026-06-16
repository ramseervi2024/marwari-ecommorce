<?php
namespace RestaurantManagementApi\Repositories;

class TableRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_tables';
    }
}
