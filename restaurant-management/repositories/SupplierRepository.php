<?php
namespace RestaurantManagementApi\Repositories;

class SupplierRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_suppliers';
    }
}
