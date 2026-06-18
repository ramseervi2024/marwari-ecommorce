<?php
namespace CrmManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class MeetingRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('meetings', false);
    }
}
