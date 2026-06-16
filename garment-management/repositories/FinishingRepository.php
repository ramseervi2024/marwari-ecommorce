<?php
namespace GarmentManagementApi\Repositories;

class FinishingRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_finishing';
    }
}
