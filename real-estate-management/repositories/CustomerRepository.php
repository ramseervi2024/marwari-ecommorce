<?php
namespace RealEstateManagementApi\Repositories;

class CustomerRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('customers');
    }

    public function existsCustomerCode(string $customer_code, ?int $exclude_id = null): bool {
        return $this->exists('customer_code', $customer_code, $exclude_id);
    }
}
