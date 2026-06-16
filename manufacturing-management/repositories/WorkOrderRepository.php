<?php
namespace ManufacturingManagementApi\Repositories;

class WorkOrderRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_work_orders';
    }
}
