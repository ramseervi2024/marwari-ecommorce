<?php
namespace HospitalManagementApi\Repositories;

class DoctorRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('doctors');
    }

    public function existsDoctorCode(string $doctor_code, ?int $exclude_id = null): bool {
        return $this->exists('doctor_code', $doctor_code, $exclude_id);
    }
}
