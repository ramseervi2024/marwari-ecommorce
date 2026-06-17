<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class ChallanRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('challans');
    }

    public function existsChallanNumber(string $challan_number, ?int $exclude_id = null): bool {
        return $this->exists('challan_number', $challan_number, $exclude_id);
    }
}
