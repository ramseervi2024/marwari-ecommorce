<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class DealerRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('dealers', true);
    }
}
