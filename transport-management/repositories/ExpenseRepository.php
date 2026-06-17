<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class ExpenseRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('expenses');
    }
}
