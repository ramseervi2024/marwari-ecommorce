<?php
namespace RetailPosApi\Repositories;

class CustomerRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('customers');
    }

    public function existsCustomerCode(string $code, ?int $exclude_id = null): bool {
        return $this->exists('customer_code', $code, $exclude_id);
    }
}
