<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class SalesRepRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('sales_reps', true);
    }
}
