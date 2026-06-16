<?php
namespace GarmentManagementApi\Repositories;

class CuttingRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_cutting';
    }
}
