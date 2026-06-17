<?php
namespace RealEstateManagementApi\Repositories;

class PaymentScheduleRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('payment_schedules');
    }
}
