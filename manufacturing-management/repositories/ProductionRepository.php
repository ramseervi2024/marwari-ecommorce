<?php
namespace ManufacturingManagementApi\Repositories;

class ProductionRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_production';
    }
}
