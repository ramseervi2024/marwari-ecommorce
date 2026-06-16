<?php
namespace GarmentManagementApi\Repositories;

class MachineRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_machines';
    }
}
