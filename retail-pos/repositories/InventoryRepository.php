<?php
namespace RetailPosApi\Repositories;

class InventoryRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('inventory');
    }
}
