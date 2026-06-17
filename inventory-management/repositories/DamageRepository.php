<?php
namespace InventoryManagementApi\Repositories;

class DamageRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('damaged_stock', true);
    }
}
