<?php
namespace RestaurantManagementApi\Repositories;

class BranchRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_branches';
    }
}
