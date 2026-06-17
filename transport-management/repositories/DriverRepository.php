<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class DriverRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('drivers');
    }

    public function existsDriverCode(string $driver_code, ?int $exclude_id = null): bool {
        return $this->exists('driver_code', $driver_code, $exclude_id);
    }
}
