<?php
namespace RealEstateManagementApi\Repositories;

class BrokerRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('brokers');
    }

    public function existsBrokerCode(string $broker_code, ?int $exclude_id = null): bool {
        return $this->exists('broker_code', $broker_code, $exclude_id);
    }
}
