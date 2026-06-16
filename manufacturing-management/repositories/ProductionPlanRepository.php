<?php
namespace ManufacturingManagementApi\Repositories;

class ProductionPlanRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_production_plans';
    }
}
