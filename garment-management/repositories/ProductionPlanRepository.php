<?php
namespace GarmentManagementApi\Repositories;

class ProductionPlanRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_production_plans';
    }
}
