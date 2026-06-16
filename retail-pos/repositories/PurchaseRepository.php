<?php
namespace RetailPosApi\Repositories;

class PurchaseRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('purchases');
    }
}
