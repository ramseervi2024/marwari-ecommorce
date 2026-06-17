<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class DocumentRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('documents');
    }
}
