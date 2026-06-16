<?php
namespace GarmentManagementApi\Repositories;

class WastageRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_wastage';
    }
}
