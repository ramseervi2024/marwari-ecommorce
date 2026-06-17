<?php
namespace TransportManagementApi\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class RouteRepository extends BaseRepository {
    public function __construct() {
        parent::__construct('routes');
    }

    public function existsRouteCode(string $route_code, ?int $exclude_id = null): bool {
        return $this->exists('route_code', $route_code, $exclude_id);
    }
}
