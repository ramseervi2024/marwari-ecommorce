<?php
namespace JewelleryManagementApi\Repositories;

class BillingRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_billing';
    }
}
