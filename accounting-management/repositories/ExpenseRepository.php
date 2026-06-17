<?php
namespace AccountingManagementApi\Repositories;

class ExpenseRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('expenses', true);
    }
}
