<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class FuelRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('fuel');
    }
}
