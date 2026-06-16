<?php
namespace ManufacturingManagementApi\Repositories;

class WarehouseRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_warehouses';
    }
}
