<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class MaintenanceRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('maintenance');
    }
}
