<?php
namespace RealEstateManagementApi\Repositories;

class LeadRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('leads');
    }

    public function existsLeadNumber(string $lead_number, ?int $exclude_id = null): bool {
        return $this->exists('lead_number', $lead_number, $exclude_id);
    }
}
