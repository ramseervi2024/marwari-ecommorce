<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class InventoryRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('inventory', true);
    }
}
