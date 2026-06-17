<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class VehicleRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('vehicles');
    }

    public function existsVehicleNumber(string $vehicle_number, ?int $exclude_id = null): bool {
        return $this->exists('vehicle_number', $vehicle_number, $exclude_id);
    }
}
