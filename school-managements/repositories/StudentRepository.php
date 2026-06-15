<?php
namespace SchoolManagementApi\Repositories;

class StudentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('students');
    }

    public function existsAdmissionNo(string $admission_no, ?int $exclude_id = null): bool {
        return $this->exists('admission_no', $admission_no, $exclude_id);
    }
}
