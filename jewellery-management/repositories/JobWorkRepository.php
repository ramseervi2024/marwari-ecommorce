<?php
namespace JewelleryManagementApi\Repositories;

class JobWorkRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_job_work';
    }
}
