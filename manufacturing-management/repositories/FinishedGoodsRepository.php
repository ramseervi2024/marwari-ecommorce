<?php
namespace ManufacturingManagementApi\Repositories;

class FinishedGoodsRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_finished_goods';
    }
}
