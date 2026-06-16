<?php
namespace GarmentManagementApi\Repositories;

class WorkerRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_workers';
    }
}
