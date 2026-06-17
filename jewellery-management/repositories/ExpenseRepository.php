<?php
namespace JewelleryManagementApi\Repositories;

class ExpenseRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_expenses';
    }
}
