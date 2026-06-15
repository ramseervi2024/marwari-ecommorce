<?php
namespace SchoolManagementApi\Repositories;

class TeacherRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('teachers');
    }

    public function existsEmployeeCode(string $employee_code, ?int $exclude_id = null): bool {
        return $this->exists('employee_code', $employee_code, $exclude_id);
    }
}
