<?php
namespace GarmentManagementApi\Repositories;

class StitchingRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_stitching';
    }
}
