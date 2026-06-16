<?php
namespace GarmentManagementApi\Repositories;

class FabricRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_fabrics';
    }
}
