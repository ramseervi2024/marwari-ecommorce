<?php
namespace ManufacturingManagementApi\Repositories;

class JobWorkRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_job_work';
    }
}
