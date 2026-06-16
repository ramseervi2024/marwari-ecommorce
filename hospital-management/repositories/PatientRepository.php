<?php
namespace HospitalManagementApi\Repositories;

class PatientRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('patients');
    }

    public function existsPatientCode(string $patient_code, ?int $exclude_id = null): bool {
        return $this->exists('patient_code', $patient_code, $exclude_id);
    }
}
