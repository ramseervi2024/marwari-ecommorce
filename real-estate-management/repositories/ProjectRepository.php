<?php
namespace RealEstateManagementApi\Repositories;

class ProjectRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('projects');
    }

    public function existsProjectCode(string $project_code, ?int $exclude_id = null): bool {
        return $this->exists('project_code', $project_code, $exclude_id);
    }
}
