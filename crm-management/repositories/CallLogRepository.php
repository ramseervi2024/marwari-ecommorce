<?php
namespace CrmManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class CallLogRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('call_logs', false);
    }
}
