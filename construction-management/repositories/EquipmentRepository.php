<?php
namespace ConstructionManagementApi\Repositories;

class EquipmentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('equipment');
    }

    public function existsEquipmentCode(string $equipment_code, ?int $exclude_id = null): bool {
        return $this->exists('equipment_code', $equipment_code, $exclude_id);
    }
}
