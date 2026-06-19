<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class CreditLimitRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('credit_limits', true);
    }
}
