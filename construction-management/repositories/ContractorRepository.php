<?php
namespace ConstructionManagementApi\Repositories;

class ContractorRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('contractors');
    }

    public function existsContractorCode(string $contractor_code, ?int $exclude_id = null): bool {
        return $this->exists('contractor_code', $contractor_code, $exclude_id);
    }
}
