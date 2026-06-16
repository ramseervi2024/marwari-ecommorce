<?php
namespace GarmentManagementApi\Repositories;

class SupplierRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_suppliers';
    }
}
