<?php
namespace HospitalManagementApi\Repositories;

class LaboratoryRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('lab_reports');
    }
}
