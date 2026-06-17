<?php
namespace ServiceManagementApi\Repositories;

class PaymentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('payments', false);
    }

    public function existsPaymentNumber(string $num, ?int $exclude_id = null): bool {
        return $this->exists('payment_number', $num, $exclude_id);
    }
}
