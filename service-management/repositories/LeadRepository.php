<?php
namespace ServiceManagementApi\Repositories;

class LeadRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('leads', true);
    }
}
