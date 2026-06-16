<?php
namespace GarmentManagementApi\Repositories;

class PurchaseRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_purchases';
    }
}
