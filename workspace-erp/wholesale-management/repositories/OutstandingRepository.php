<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class OutstandingRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('outstandings', true);
    }
}
