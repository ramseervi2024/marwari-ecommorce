<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class PaymentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('payments', true);
    }
}
