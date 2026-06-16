<?php
namespace RestaurantManagementApi\Repositories;

class ExpenseRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'restaurant_expenses';
    }
}
