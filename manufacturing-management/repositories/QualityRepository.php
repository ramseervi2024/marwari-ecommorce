<?php
namespace ManufacturingManagementApi\Repositories;

class QualityRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_quality';
    }
}
