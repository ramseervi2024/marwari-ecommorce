<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class SupplierRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('suppliers', true);
    }
}
