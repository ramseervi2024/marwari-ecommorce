<?php
namespace ConstructionManagementApi\Repositories;

class MaterialRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('materials');
    }

    public function existsMaterialCode(string $material_code, ?int $exclude_id = null): bool {
        return $this->exists('material_code', $material_code, $exclude_id);
    }
}
