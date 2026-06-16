<?php
namespace ManufacturingManagementApi\Repositories;

class MachineRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_machines';
    }
}
