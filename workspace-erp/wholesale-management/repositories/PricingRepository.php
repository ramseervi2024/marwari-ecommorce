<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class PricingRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('pricing', true);
    }
}
