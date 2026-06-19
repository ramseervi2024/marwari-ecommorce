<?php
namespace WholesaleErp\Repositories;

if (!defined('ABSPATH')) exit;

class ProductRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('products', true);
    }
}
