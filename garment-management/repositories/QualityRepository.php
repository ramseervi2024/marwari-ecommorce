<?php
namespace GarmentManagementApi\Repositories;

class QualityRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_quality';
    }
}
