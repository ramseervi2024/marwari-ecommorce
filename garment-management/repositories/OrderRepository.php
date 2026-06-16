<?php
namespace GarmentManagementApi\Repositories;

class OrderRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_orders';
    }
}
