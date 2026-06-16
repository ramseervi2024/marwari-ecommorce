<?php
namespace ManufacturingManagementApi\Repositories;

class PurchaseRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'mfg_purchases';
    }
}
