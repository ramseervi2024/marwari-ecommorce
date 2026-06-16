<?php
namespace RestaurantManagementApi\Repositories;

class StaffRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_staff';
    }
}
