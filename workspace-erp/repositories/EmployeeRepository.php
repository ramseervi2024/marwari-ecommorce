<?php
namespace WorkspaceErpApi\Repositories;

class EmployeeRepository extends BaseRepository {
    public function __construct() { parent::__construct('employees'); }
    public function existsEmployeeCode(string $code, ?int $exclude_id = null): bool { return $this->exists('employee_code', $code, $exclude_id); }
}
