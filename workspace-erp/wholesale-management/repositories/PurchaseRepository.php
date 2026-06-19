<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class PurchaseRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('purchases', true);
    }
}
