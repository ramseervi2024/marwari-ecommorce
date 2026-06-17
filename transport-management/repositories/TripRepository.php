<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class TripRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('trips');
    }

    public function existsTripNumber(string $trip_number, ?int $exclude_id = null): bool {
        return $this->exists('trip_number', $trip_number, $exclude_id);
    }
}
