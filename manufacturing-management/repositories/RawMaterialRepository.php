<?php
namespace ManufacturingManagementApi\Repositories;

class RawMaterialRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_raw_materials';
    }
}
