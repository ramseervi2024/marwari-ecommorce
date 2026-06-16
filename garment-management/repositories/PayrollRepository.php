<?php
namespace GarmentManagementApi\Repositories;

class PayrollRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'garment_payroll';
    }
}
