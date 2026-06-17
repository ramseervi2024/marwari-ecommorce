<?php
namespace ServiceManagementApi\Repositories;

class AmcRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('amc', true);
    }

    public function existsContractNumber(string $num, ?int $exclude_id = null): bool {
        return $this->exists('contract_number', $num, $exclude_id);
    }
}
