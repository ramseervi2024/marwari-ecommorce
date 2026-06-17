<?php
namespace AccountingManagementApi\Repositories;

class PaymentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('payments', true);
    }
}
