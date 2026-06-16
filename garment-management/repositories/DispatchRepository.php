<?php
namespace GarmentManagementApi\Repositories;

class DispatchRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_dispatch';
    }
}
