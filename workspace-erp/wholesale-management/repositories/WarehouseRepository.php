<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class WarehouseRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('warehouses', true);
    }
}
