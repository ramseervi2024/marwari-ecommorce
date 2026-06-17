<?php
namespace JewelleryManagementApi\Repositories;

class CustomerRepository extends BaseRepository {
    protected function get_table_suffix(): string {
        return 'jewel_customers';
    }
}
