<?php
namespace CrmManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class TaskRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('tasks', false);
    }
}
