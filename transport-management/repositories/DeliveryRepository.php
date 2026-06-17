<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class DeliveryRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('deliveries');
    }

    public function existsTrackingNumber(string $tracking_number, ?int $exclude_id = null): bool {
        return $this->exists('tracking_number', $tracking_number, $exclude_id);
    }
}
