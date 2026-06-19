<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class BillingRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('billing', true);
    }
}
