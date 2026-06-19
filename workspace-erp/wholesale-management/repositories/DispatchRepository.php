<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class DispatchRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('dispatches', true);
    }
}
