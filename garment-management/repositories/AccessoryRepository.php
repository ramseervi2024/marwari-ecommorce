<?php
namespace GarmentManagementApi\Repositories;

class AccessoryRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_accessories';
    }
}
