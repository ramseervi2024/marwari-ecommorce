<?php
namespace ManufacturingManagementApi\Repositories;

class SupplierRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_suppliers';
    }
}
