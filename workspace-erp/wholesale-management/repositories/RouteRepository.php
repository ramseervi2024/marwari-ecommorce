<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class RouteRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('routes', true);
    }
}
