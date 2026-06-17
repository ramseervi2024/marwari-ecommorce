<?php
namespace ConstructionManagementApi\Repositories;

class SiteExpenseRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('site_expenses');
    }
}
