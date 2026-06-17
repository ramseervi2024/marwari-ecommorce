<?php
namespace ServiceManagementApi\Repositories;

class JobRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('jobs', true);
    }

    public function existsJobNumber(string $num, ?int $exclude_id = null): bool {
        return $this->exists('job_number', $num, $exclude_id);
    }
}
